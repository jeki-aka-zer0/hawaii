import React, { useState } from 'react'
import { SubmitHandler, useForm } from 'react-hook-form'
import axios from 'axios'

type Inputs = {
  name: string;
  description: string;
};

const EntityForm: React.FC = () => {
  const [isFormShown, setIsFormShown] = useState<boolean>(false)

  const { register, handleSubmit, formState: { errors } } = useForm<Inputs>()
  const onSubmit: SubmitHandler<Inputs> = data => {
    console.log(data)
    axios
      .post('http://localhost:8080/eav/entity', data)
      .then(response => {console.log(response.data)})
      .catch(error => {console.log(error.data)})
  }

  if (!isFormShown) {
    return (
      <button onClick={() => setIsFormShown(true)}>Create</button>
    )
  } else {
    return (
      <form onSubmit={handleSubmit(onSubmit)}>
        <div>
          <label htmlFor="name">
            Name
            <input type="text" {...register('name', { required: true })}/>
            {errors.name && <span>This field is required</span>}
          </label>
        </div>
        <div>
          <label htmlFor="description">
            Description
            <input type="text" {...register('description')}/>
          </label>
        </div>
        <div>
          <input type="submit" value="Create"/>
        </div>
      </form>
    )
  }
}

export default EntityForm