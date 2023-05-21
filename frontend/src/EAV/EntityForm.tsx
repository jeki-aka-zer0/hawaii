import React from 'react'
import './EntityForm.css'
import { SubmitHandler, useForm } from 'react-hook-form'
import { ErrorMessage } from '@hookform/error-message/dist';
import axios from 'axios'

type Inputs = {
  name: string;
  description: string;
};

const EntityForm: React.FC = () => {
  const { register, handleSubmit, formState: { errors, isSubmitting, isDirty, isValid } } = useForm<Inputs>({
    criteriaMode: 'all',
  })
  const onSubmit: SubmitHandler<Inputs> = (data: Inputs): void => {
    console.log(data)
    axios
      .post(`${process.env.REACT_APP_API_URL}/eav/entity`, data)
      .then(response => console.log(response.data))
      .catch(error => console.log(error.data))
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
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
          render={({ messages }) =>
            messages &&
            Object.entries(messages).map(([type, message]) => (
              <span className={'error'} key={type}>{message}</span>
            ))
          }
        />
      </div>
      <div>
        <label htmlFor="description">Description</label>
        <textarea {...register('description')} id="description" className={'size-m'}></textarea>
      </div>
      <div>
        <input type="submit" value="Create" disabled={isSubmitting && isDirty && isValid}/>
      </div>
    </form>
  )
}

export default EntityForm