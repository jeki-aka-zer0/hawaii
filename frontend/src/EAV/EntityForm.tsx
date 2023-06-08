import React, { FC } from 'react'
import './EntityForm.css'
import { FieldPath, FieldValues, SubmitHandler, useForm } from 'react-hook-form'
import { ErrorMessage } from '@hookform/error-message/dist';
import axios, { AxiosError, AxiosResponse } from 'axios'
import { CreatedEntity } from '../types/types'

type Inputs = {
  name: string;
  description: string;
};

interface ValidationErrorResponse<TFields extends FieldValues> {
  errors: Record<FieldPath<TFields>, string[]>
}

const hasOwnProperty = <T extends object> (data: T, key: any): key is keyof T => {
  return Object.prototype.hasOwnProperty.call(data, key)
}

function isValidationError<TFields extends FieldValues> (
  error: any
): error is AxiosError<ValidationErrorResponse<TFields>> {
  return axios.isAxiosError(error) && 'errors' in error.response?.data
}

const EntityForm: FC = () => {
  const { register, handleSubmit, setError, formState: { errors, isSubmitting, isDirty, isValid } } = useForm<Inputs>({
    criteriaMode: 'all'
  })
  const onSubmit: SubmitHandler<Inputs> = (data: Inputs): void => {
    axios
      .post<CreatedEntity>(`${process.env.REACT_APP_API_URL}/eav/entity`, data)
      .then((response: AxiosResponse<CreatedEntity>) => {
        console.log('then', response.data)
      })
      .catch(result => {
        if (result.response.status === 422 && isValidationError<Inputs>(result)) {
          const validationErrors = result.response!.data.errors
          for (const fieldName in validationErrors) {
            if (hasOwnProperty(validationErrors, fieldName)) {
              setError(fieldName, { message: validationErrors[fieldName].join(' ') })
            }
          }
        }
      })
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} data-testid="entity-form">
      <div>
        <label htmlFor="name">Name</label>
        <input
          id="name"
          className={'size-m'}
          type="text"
          {...register('name', {
            required: 'Name is required.',
            minLength: { value: 2, message: 'Name must be at least 2 characters.' },
            maxLength: { value: 255, message: 'Name must be at max 255 characters.' }
          })}
        />
        <ErrorMessage
          errors={errors}
          name="name"
          render={({ message }) => <span className={'error'}>{message}</span>}
        />
      </div>
      <div>
        <label htmlFor="description">Description</label>
        <textarea {...register('description')} id="description" className={'size-m'}></textarea>
      </div>
      <div>
        <input type="submit" value="Create" disabled={isSubmitting && isDirty && isValid} data-testid="entity-form-submit-btn"/>
      </div>
    </form>
  )
}

export default EntityForm