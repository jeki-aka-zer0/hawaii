import React, {FC, useEffect, useRef, useState} from 'react'
import { SubmitHandler, useForm } from 'react-hook-form'
import { ErrorMessage } from '@hookform/error-message/dist'
import axios, { AxiosResponse } from 'axios'
import {Attr, CreatedEntity, FormErrors } from '../../types/types'
import { hasOwnProperty, isValidationError } from '../../utils/utils'
import { NavigateFunction, useNavigate } from 'react-router-dom'

type Inputs = {
  name: string;
  description: string;
};

const EntityForm: FC = () => {
  const { register, handleSubmit, setError, formState: { errors, isSubmitting, isDirty, isValid } } = useForm<Inputs>({
    criteriaMode: 'all'
  })
  const [attrs, setAttrs] = useState<Attr[]>([])
  const navigate: NavigateFunction = useNavigate();
  const effectRun = useRef(false);

  useEffect(() => {
    const controller : AbortController = new AbortController()
    if (effectRun.current) {
      axios
          .get(`${process.env.REACT_APP_API_URL}/eav/attribute`, {
            signal: controller.signal
          })
          .then(res => {
            console.log(res.data.attributes)
            setAttrs(res.data.attributes)
          })
          .catch(error => console.log(error))
    }

    return (): void => {
      controller.abort()
      effectRun.current = true;
    }
  }, [])

  const onSubmit: SubmitHandler<Inputs> = async (data: Inputs): Promise<void> => {
    try {
      const response: AxiosResponse<CreatedEntity> = await axios.post(`${process.env.REACT_APP_API_URL}/eav/entity`, data)

      if (response.status === 201) {
        const createdEntity: CreatedEntity = response.data;
        const entityId = createdEntity.entity_id
        navigate(`/entity/${entityId}`)
      } else {
        alert('Unexpected server response')
      }
    } catch (err: any) {
      let errorShown: boolean = false
      if (err.response.status === 422 && isValidationError<Inputs>(err)) {
        const validationErrors: FormErrors = err.response!.data.errors
        for (const fieldName in validationErrors) {
          if (hasOwnProperty(validationErrors, fieldName)) {
            errorShown = true
            console.log(fieldName , "name", validationErrors[fieldName].join(' '))
            setError(fieldName as keyof Inputs, { message: validationErrors[fieldName].join(' ') })
          }
        }
      }

      if (!errorShown) {
        alert('Unexpected server response')
      }
    }
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} data-testid="entity-form">
      <div>
        <label htmlFor="name">Name</label>
        <input
          id="name"
          className={'size-m'}
          type="text"
          {...register<keyof Inputs>('name', {
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
        <textarea {...register<keyof Inputs>('description')} id="description" className={'size-m'}></textarea>
      </div>
      <div>
        <label htmlFor="attributes">Attribute</label>
        <input
            id="attributes"
            className={'size-s'}
            type="text"
            list="Languages"
        />
        {attrs.length && <datalist id="Languages">
          {attrs.map((a: Attr) => <option key={a.attribute_id} value={a.name}/>)}
        </datalist>}
      </div>
      <div>
        <input type="submit" value="Create" disabled={isSubmitting && isDirty && isValid} data-testid="entity-form-submit-btn"/>
      </div>
    </form>
  )
}

export default EntityForm