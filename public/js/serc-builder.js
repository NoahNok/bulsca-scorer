class SERCBuilder {
    constructor(target) {
        this.element = target;

        this.builder_id = target.getAttribute("serc-builder-id");
        this.builder_name = target.getAttribute("serc-builder");
        this.serc_name = document.querySelector("[serc-builder-name]");
        this.serc_type = document.querySelector("[serc-builder-type]");
        this.url = target.getAttribute("serc-builder-url");
        this.after = target.getAttribute("serc-builder-after-url");
        this.csrf = target.getAttribute("serc-builder-csrf");

        this.judges = [];

        this.deleted = {
            judges: [],
            marking_points: [],
        };

        let clazz = this;

        target.querySelectorAll("[serc-builder-judge]").forEach((j) => {
            clazz.judges.push(new SERCJudge(j, clazz.judges, clazz.deleted));
        });

        this.add_judge = target.querySelector("[serc-builder-judge-add]");

        this.add_judge.onclick = (e) => {
            let newJudge = clazz.element
                .querySelector("[serc-builder-judge]")
                .cloneNode(true);

            // Reset any values
            newJudge.setAttribute("serc-builder-judge-id", "null");

            newJudge.querySelector(
                "[serc-builder-judge-description]"
            ).innerHTML = "";

            newJudge.querySelectorAll("input").forEach((i) => {
                i.value = null;
            });

            let mps = newJudge.querySelector("[serc-builder-marking-points]");
            let mp = mps
                .querySelector("[serc-builder-marking-point]")
                .cloneNode(true);
            mp.setAttribute("serc-builder-marking-point", "null");
            mps.innerHTML = "";
            mps.appendChild(mp);

            clazz.element.insertBefore(newJudge, clazz.add_judge);

            clazz.judges.push(
                new SERCJudge(newJudge, clazz.judges, clazz.deleted)
            );

            window.scrollTo(0, document.body.scrollHeight);
        };

        let saveButton = document.body.querySelector("[serc-builder-save]");

        if (saveButton)
            saveButton.onclick = (e) => {
                clazz.save();
            };

        this.serc_name.onkeydown = (e) => {
            if (clazz.serc_name.parentElement.classList.contains("is-invalid"))
                clazz.serc_name.parentElement.classList.remove("is-invalid");
        };
    }

    save() {
        if (this.serc_name.value == "") {
            this.serc_name.parentElement.classList.add("is-invalid");
            return;
        }

        let data = {
            serc_id: this.builder_id,
            serc_name: this.serc_name.value,
            serc_type: this.serc_type.value,
            judges: [],
            deleted: this.deleted,
        };

        let index = 1;
        this.judges.forEach((judge) => {
            data.judges.push(judge.toData(index));
            index++;
        });

        console.log(data);

        let fd = new FormData();

        fd.append("_token", this.csrf);
        fd.append("data", JSON.stringify(data));

        fetch(this.url, {
            method: "POST",
            body: fd,
        })
            .then((res) => res.json())
            .then((data) => {
                let id = data.sid;

                let url = this.after.replace(":rep:", id);

                window.location.href = url;
            });
    }
}

class SERCJudge {
    constructor(element, judges_list, deleted) {
        this.element = element;
        this.judge_id = element.getAttribute("serc-builder-judge-id");
        this.judge_delete = element.querySelector(
            "[serc-builder-judge-delete]"
        );
        this.judge_name = element.querySelector("[serc-builder-judge-name]");
        this.add_marking_point = element.querySelector(
            "[serc-builder-marking-point-add]"
        );

        this.marking_points = [];

        this.marking_point_container = element.querySelector(
            "[serc-builder-marking-points]"
        );

        let clazz = this;

        this.marking_point_container
            .querySelectorAll("[serc-builder-marking-point]")
            .forEach((mp) => {
                clazz.marking_points.push(
                    new SERCMarkingPoint(mp, clazz.marking_points, deleted)
                );
            });

        this.add_marking_point.onclick = (e) => {
            let newPoint = clazz.marking_point_container
                .querySelector("[serc-builder-marking-point]")
                .cloneNode(true);
            newPoint.setAttribute("serc-builder-marking-point", "null");
            newPoint.querySelectorAll("input").forEach((i) => {
                i.value = null;
                if (i.type == "text") {
                    i.placeholder = `Marking Point ${
                        clazz.marking_points.length + 1
                    }`;
                }
            });
            newPoint.querySelectorAll("label").forEach((l) => {
                l.outerHTML = "";
            });

            clazz.marking_point_container.appendChild(newPoint);

            clazz.marking_points.push(
                new SERCMarkingPoint(newPoint, clazz.marking_points, deleted)
            );
        };

        this.judge_delete.onclick = (e) => {
            if (judges_list.length == 1) return;

            if (clazz.judge_id != "null") {
                deleted.judges.push(clazz.judge_id);
            }
            clazz.element.outerHTML = "";
            judges_list.splice(judges_list.indexOf(clazz), 1);
        };

        this.initDescription();
    }

    initDescription() {
        let judge_description = this.element.querySelector(
            "[serc-builder-judge-description]"
        );

        this.judge_description = new Quill(judge_description, {
            theme: "snow",
            placeholder: "Enter marking hints/help/specification here.",
            modules: {
                toolbar: [
                    ["bold", "italic", "underline"],
                    [
                        {
                            list: "ordered",
                        },
                        {
                            list: "bullet",
                        },
                    ],
                ],
            },
        });
    }

    toData(judgeNo) {
        let judge = {
            id: this.judge_id,
            name:
                this.judge_name.value != ""
                    ? this.judge_name.value
                    : `Objective ${judgeNo}`,
            description: this.judge_description.getSemanticHTML(),
            marking_points: [],
        };

        this.marking_points.forEach((mp) => {
            let mpd = mp.toData();
            if (mpd == null) return;
            judge.marking_points.push(mpd);
        });

        return judge;
    }
}

class SERCMarkingPoint {
    constructor(element, mp_list, deleted) {
        this.element = element;
        this.id = element.getAttribute("serc-builder-marking-point");
        this.description = element.querySelector(
            "[serc-builder-marking-point-desc]"
        );
        this.weight = element.querySelector(
            "[serc-builder-marking-point-weight]"
        );
        this.delete = element.querySelector(
            "[serc-builder-marking-point-delete]"
        );

        let clazz = this;

        this.delete.onclick = (e) => {
            if (mp_list.indexOf(this) == 0) return;

            if (clazz.id != "null") {
                deleted.marking_points.push(clazz.id);
            }
            mp_list.splice(mp_list.indexOf(this), 1);
            clazz.element.outerHTML = "";
        };
    }

    toData() {
        // Skip any with missing values
        if (this.description.value == "" || this.weight.value == "")
            return null;

        return {
            id: this.id,
            description: this.description.value,
            weight: this.weight.value,
        };
    }
}
