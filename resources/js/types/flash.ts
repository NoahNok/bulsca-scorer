import { ToastVariant } from "@/lib/toast.svelte"


export enum FlashActionType {
    OVERALL_NOTES = 'overall_notes'
}

export type FlashAction = {
    type: FlashActionType
    data: any
}

export type FlashToast = {
    variant: ToastVariant
    title: string
}