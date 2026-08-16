import type { JudgeMarks } from "@/types/base";
import { writable } from "svelte/store";

export const judgeMarks = writable<JudgeMarks[]>(

);