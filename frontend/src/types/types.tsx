export interface ListResponse {
  results: Entity[]
  previous: string | null
  next: string | null
}

export type CreatedEntity = {
  readonly entity_id: string
}

type attributeValue = {
  name: string
  value: string|number
}

export type Entity = {
  readonly entity_id: string
  readonly name: string
  readonly description: string | null
  readonly attributes_values: attributeValue[]
}

export type FormErrors = {
  [key: string]: string[]
}
