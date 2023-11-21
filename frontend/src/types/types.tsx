export interface ListResponse {
  results: Entity[]
  previous: string | null
  next: string | null
}

export type CreatedEntity = {
  readonly entity_id: string
}

type attrVal = {
  name: string
  value: string|number
}

export type Entity = {
  readonly entity_id: string
  readonly name: string
  readonly description: string | null
  readonly attributes_values: attrVal[]
}

type val = {
  value_id: string
  value: string|number
}

export type Attr = {
  attribute_id: string
  name: string
  values: val[]
}

export type FormErrors = {
  [key: string]: string[]
}
