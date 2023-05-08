import React from 'react'
import './EntityForm.css'
import { SubmitHandler, useForm } from 'react-hook-form'
import axios from 'axios'

type Inputs = {
  name: string;
  description: string;
};

const EntityForm: React.FC = () => {
  const { register, handleSubmit, formState: { errors, isSubmitting, isDirty, isValid } } = useForm<Inputs>()
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
        <input type="text" {...register('name', { required: true, minLength: 2 })} id="name" className={'size-m'}/>
        {errors.name && <span className={'error'}>Name is required</span>}
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