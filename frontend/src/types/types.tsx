export interface ListResponse {
    results: Entity[]
    previous: string | null
    next: string | null
}

export type Entity = {
    readonly name: string
    readonly description: string | null
}