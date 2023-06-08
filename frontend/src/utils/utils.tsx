import { FieldPath, FieldValues } from 'react-hook-form'
import axios, { AxiosError } from 'axios'

interface ValidationErrorResponse<TFields extends FieldValues> {
  errors: Record<FieldPath<TFields>, string[]>
}

export const hasOwnProperty = <T extends object> (data: T, key: any): key is keyof T => {
  return Object.prototype.hasOwnProperty.call(data, key)
}

export function isValidationError<TFields extends FieldValues> (
  error: any
): error is AxiosError<ValidationErrorResponse<TFields>> {
  return axios.isAxiosError(error) && 'errors' in error.response?.data
}