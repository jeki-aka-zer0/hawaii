import React, {ChangeEvent, FC, useEffect, useRef, useState} from 'react'
import {SubmitHandler, useForm} from 'react-hook-form'
import {ErrorMessage} from '@hookform/error-message/dist'
import axios, {AxiosResponse} from 'axios'
import {Attr, CreatedEntity, FormErrors, Val} from '../../types/types'
import {hasOwnProperty, isValidationError} from '../../utils/utils'
import {NavigateFunction, useNavigate} from 'react-router-dom'

type Inputs = {
  name: string;
  description: string;
  attributes_values: attrVal[]
};

type attrVal = {
  attribute_id?: string
  name: string
  value_id?: string
  value: string|number
}

const EntityForm: FC = () => {
  const { register, handleSubmit, setError, formState: { errors, isSubmitting, isDirty, isValid } } = useForm<Inputs>({
    criteriaMode: 'all'
  })
  const [attrs, setAttrs] = useState<Attr[]>([])
  const [attrMap, setAttrMap] = useState<Map<string, Attr>>(new Map<string, Attr>())
  const [attrsVal, setAttrsVal] = useState<Map<string, attrVal>>(new Map<string, attrVal>())
  const [values, setValues] = useState<(string | number)[]>([])
  const navigate: NavigateFunction = useNavigate();
  const effectRun = useRef(false);
  const [attrName, setAttrName] = useState<string>('')
  const [val, setVal] = useState<string>('')

  useEffect(() => {
    const controller : AbortController = new AbortController()
    if (effectRun.current) {
      axios
          .get(`${process.env.REACT_APP_API_URL}/eav/attribute`, {
            signal: controller.signal
          })
          .then((res: AxiosResponse<any, any>) => {
            setAttrs(res.data.attributes)
            // create lower cased attribute name to attribute_id map
            const attrToIdMap = new Map<string, Attr>()
            const values: (string | number)[] = []
            res.data.attributes.forEach((attr: Attr) => {
              attr.values.map(v => values.push(v.value))
              attrToIdMap.set(attr.name.toLowerCase(), attr)
            })
            setValues(values)
            setAttrMap(attrToIdMap)
          })
          .catch(error => console.log(error))
    }

    return (): void => {
      controller.abort()
      effectRun.current = true;
    }
  }, [])

  const onSubmit: SubmitHandler<Inputs> = async (data: Inputs): Promise<void> => {
    const attributesValues: attrVal[] = []
    attrsVal.forEach((attrVal: attrVal) => {
      attributesValues.push({
        "attribute_id": attrVal.attribute_id,
        "name": attrVal.name,
        "value": attrVal.value
      })
    })
    data.attributes_values = attributesValues

    try {
      const response: AxiosResponse<CreatedEntity> = await axios.post(`${process.env.REACT_APP_API_URL}/eav/entity`, data)

      if (response.status === 201) {
        const createdEntity: CreatedEntity = response.data;
        navigate(`/entity/${createdEntity.entity_id}`)
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
            setError(fieldName as keyof Inputs, { message: validationErrors[fieldName].join(' ') })
          }
        }
      }

      if (!errorShown) {
        alert('Unexpected server response')
      }
    }
  }

  const addAttrVal = (e: React.MouseEvent<HTMLButtonElement>): void => {
    e.preventDefault();

    if (attrName.length === 0 || val.length === 0) {
      // setError("attributes_values" as keyof Inputs, { message: "Attribute cannot be empty" })
      return
    }

    const key: string = attrName.toLowerCase()
    const attrVal: attrVal = {
      attribute_id: attrMap.get(key)?.attribute_id,
      name: attrName,
      value: val,
    }

    const rows: Map<string, attrVal> = attrsVal
    rows.set(key, attrVal)
    setAttrsVal(new Map<string, attrVal>(rows))

    // reset attribute and value
    setAttrName("")
    setVal("")
  }

  const handleAttrChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const values: (string | number)[] = []
    if (e.target.value.length >= 3) {
      const key: string = e.target.value.toLowerCase()
      if (attrMap.has(key)) {
        attrMap.get(key)?.values.map((v: Val) => values.push(v.value))
      }
    }

    setAttrName(e.target.value)
    setValues(values)
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
        {[...attrsVal.keys()].map((key: string, i: number) => {
          const attrVal: attrVal | undefined = attrsVal.get(key)
          return attrVal !== undefined &&
              <span className={"tag"} title={attrVal.name} key={attrVal.name + attrVal.value}>{attrVal.value}</span>
        })}
      </div>
      <div>
        <label htmlFor="attribute">Attribute</label>
        <input
            id="attribute"
            className={'size-s'}
            type="text"
            list="Languages"
            value={attrName}
            onChange={handleAttrChange}
        />
        {attrs.length > 0 && <datalist id="Languages">
          {attrs.map((a: Attr) => <option key={a.attribute_id} value={a.name}/>)}
        </datalist>}
        <label htmlFor="value">Value</label>
        <input
            id="value"
            className={'size-s'}
            type="text"
            list="Values"
            value={val}
            onChange={(e: ChangeEvent<HTMLInputElement>) => setVal(e.target.value)}
        />
        {values.length > 0 && <datalist id="Values">
          {values.map((v: string | number, i) => <option key={i} value={v}/>)}
        </datalist>}
        <button title="Add attribute with value" onClick={addAttrVal}>+</button>
        <div>
          <span className="error">{/* todo show error */}</span>
        </div>
      </div>
      <div>
        <input type="submit" value="Create" disabled={isSubmitting && isDirty && isValid} data-testid="entity-form-submit-btn"/>
      </div>
    </form>
  )
}

export default EntityForm