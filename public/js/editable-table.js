



class EditableTable {

    constructor(target) {
        this.rows = []
        this.element = target
        this.url = target.getAttribute('table-submit-url')
        this.after = target.getAttribute('table-after-url')
        this.csrf = target.getAttribute('table-submit-csrf')
        let clazz = this
        target.querySelectorAll("[table-row]").forEach(row => {
            clazz.rows.push(new EditableTableRow(row))
        });

        let saveButton = document.querySelector(`[table-submit='${target.getAttribute('editable-table')}']`)
        saveButton.onclick = (e) => {
            this.save()
        }

        let addRow = document.querySelector(`[table-add-row='${target.getAttribute('editable-table')}']`)
        addRow.onclick = (e) => {
            this.addRow()
        }
    }

    save() {
        let data = []
        this.rows.forEach(row => {
            let d = row.getData();
            if (d == null) return
            data.push(d)
            row.setValidCells()
        })

        


        let fd = new FormData();
        fd.append('_token', this.csrf)
        fd.append('data', JSON.stringify(data))

        let clazz = this
        

        fetch(this.url, {

            method: 'POST',
            body: fd
        }).then(async res => {
            if (res.status == 200) window.location.href = clazz.after
            if (res.status == 500) this.handleErrors(await res.json())
        })
    }

    getRow(id) {
        let found = null;

        this.rows.forEach(row => {
            if (row.owner == id) {
                found = row
                return
            }
        })

        return found
    }

    async handleErrors(errors) {
        
        showAlert("Something went wrong, please check your entries are correct!")

        Object.keys(errors).forEach(i => {
            let invalid = errors[i]
            let id = invalid.id;
            let name = invalid.option;
            
            let row = this.getRow(id)

            row.setInvalidCell(name)
        })




    }

    addRow() {
        console.log('h')
        let tbody = this.element.querySelector('tbody')
        let row = tbody.querySelector('tr')

        let newRow = row.cloneNode(true)

        newRow.setAttribute("table-row-owner", null)
        newRow.querySelectorAll("input").forEach(input => {
            input.value = null
            
        })


        console.log(newRow)

        tbody.appendChild(newRow);

        this.rows.push(new EditableTableRow(newRow))

        
    }

    
}

class EditableTableRow {

    constructor(element) {
        this.cells = []

        this.owner = element.getAttribute('table-row-owner')
  

        let clazz = this;
        element.querySelectorAll("[table-cell]").forEach(cell => {
            clazz.cells.push(new EditableTableCell(cell));
        })

        console.log(`TABLE ROW\nOwner: ${this.owner}\nCell names: ${this.cells.join(',')}`)
        

       
    }

    getData() {
        let data = {
            id: this.owner,
            values: {}
        }

        let nulls = 0

        this.cells.forEach(cell => {
            let d = cell.getData();
            data.values[d.key] = d.value
            if (d.value == "" || d.value == "null") nulls++
        })

        if (nulls != 0) return null;


        return data;
    }

    setInvalidCell(name) {
        this.cells.forEach(cell => {
            if (cell.name == name) cell.element.classList.add('invalid')
        })
    }

    setValidCells() {
        this.cells.forEach(cell => {
            cell.element.classList.remove('invalid')
        })
    }

}

class EditableTableCell {


    constructor(element) {
        this.element = element
        this.name = element.getAttribute("table-cell-name")
       

      

    }

    getData() {
        return{key: this.name, value: this.element.value};
    }

    toString() {
        return this.name
    }

    

}

window.onload = () => {
    document.querySelectorAll("[editable-table]").forEach(et => {
        new EditableTable(et)
    })
}