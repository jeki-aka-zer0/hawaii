import React, {ChangeEvent, FC, useEffect, useRef, useState} from 'react'
import {SubmitHandler, useForm} from 'react-hook-form'
import {ErrorMessage} from '@hookform/error-message/dist'
import axios, {AxiosResponse} from 'axios'
import {Attr, AttrVal, CreatedEntity, FormErrors} from '../../types/types'
import {hasOwnProperty, isValidationError} from '../../utils/utils'
import {NavigateFunction, useNavigate} from 'react-router-dom'

type Inputs = {
  name: string;
  description: string;
  attributes_values: AttrVal[]
};

const EntityForm: FC = () => {
  const { register, handleSubmit, setError, formState: { errors, isSubmitting, isDirty, isValid } } = useForm<Inputs>({
    criteriaMode: 'all'
  })
  const [attrs, setAttrs] = useState<Attr[]>([])
  const [attrMap, setAttrMap] = useState<Map<string, Attr>>(new Map<string, Attr>())
  const [attrsVal, setAttrsVal] = useState<Map<string, AttrVal>>(new Map<string, AttrVal>())
  const [values, setValues] = useState<(string | number)[]>([])
  const [areAllValuesShown, setAreAllValuesShown] = useState<boolean>(true)
  const navigate: NavigateFunction = useNavigate();
  const effectRun = useRef(false);
  const [attrName, setAttrName] = useState<string>('')
  const [val, setVal] = useState<string>('')

  useEffect(() => {
    const controller: AbortController = new AbortController()
    if (effectRun.current) {
      axios
          .get(`${process.env.REACT_APP_API_URL}/eav/attribute`, {
            signal: controller.signal
          })
          .then((res: AxiosResponse<any, any>): void => {
            setAttrs(res.data.attributes)
            // create lower cased attribute name to attribute_id map
            const attrMapNew: Map<string, Attr> = new Map<string, Attr>()
            const valMap: Map<(string | number), boolean> = new Map<string, boolean>()
            res.data.attributes.forEach((attr: Attr): void => {
              attrMapNew.set(attr.name.toLowerCase(), attr)
              attr.values.map((v: string | number) => valMap.set(v, true))
            })
            setValues([...valMap.keys()])
            setAttrMap(attrMapNew)
          })
          .catch(error => console.log(error))
    }

    return (): void => {
      controller.abort()
      effectRun.current = true;
    }
  }, [])

  const onSubmit: SubmitHandler<Inputs> = async (data: Inputs): Promise<void> => {
    data.attributes_values = []
    attrsVal.forEach((attrVal: AttrVal): void => {
      data.attributes_values.push({
        attribute_id: attrVal.attribute_id,
        name: attrVal.name,
        value: attrVal.value
      })
    })

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
    const attrsValNew: Map<string, AttrVal> = attrsVal
    attrsValNew.set(key, {
      attribute_id: attrMap.get(key)?.attribute_id,
      name: attrName,
      value: val,
    })
    setAttrsVal(new Map<string, AttrVal>(attrsValNew))

    // reset attribute and value inputs
    setAttrName("")
    setVal("")
  }

  const handleAttrChange = (e: React.ChangeEvent<HTMLInputElement>): void => {
    const valMap: Map<(string | number), boolean> = new Map<string, boolean>()

    // if such an attribute exist, prefill with its values
    // otherwise prefill with all the values
    if (e.target.value.length >= 3) {
      const name: string = e.target.value.toLowerCase()
      attrMap.get(name)?.values.map((v: (string | number)) => valMap.set(v, true))
      setAreAllValuesShown(false)
    } else if (!areAllValuesShown) {
      attrs.forEach((attr: Attr): void => {
        attr.values.map((v: string | number) => valMap.set(v, true))
      })
      setAreAllValuesShown(true)
    }

    setAttrName(e.target.value)
    setValues([...valMap.keys()])
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} data-testid="entity-form">
      <div className={'row'}>
        <div className={'col-6'}>
          <label htmlFor="name">Name</label>
          <input
              id="name"
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
              render={({message}) => <span className={'error'}>{message}</span>}
          />
        </div>
      </div>
      {attrsVal.size > 0 && <div className={'row'}>
        <div className={'col-12'}>
          {[...attrsVal.keys()].map((key: string) => {
            const attrVal: AttrVal | undefined = attrsVal.get(key)
            return attrVal !== undefined &&
                <span className={"tag"} title={attrVal.name} key={attrVal.name + attrVal.value}>{attrVal.value}</span>
          })}
        </div>
      </div>}
      <div className={'row'}>
        <div className={'col-12'}>
          <label htmlFor="description">Description</label>
          <textarea {...register<keyof Inputs>('description')} id="description"></textarea>
        </div>
      </div>
      <div className={'row'}>
        <div className={'col-3'}>
          <label htmlFor="attribute">Attribute</label>
          <input
              id="attribute"
              type="text"
              list="Attributes"
              value={attrName}
              onChange={handleAttrChange}
          />
          {<datalist id="Attributes">
            {attrs.map((a: Attr) => <option key={a.attribute_id} value={a.name}/>)}
          </datalist>}
        </div>
        <div className={'col-3'}>
          <label htmlFor="value">Value</label>
          <input
              id="value"
              type="text"
              list="Values"
              value={val}
              onChange={(e: ChangeEvent<HTMLInputElement>): void => setVal(e.target.value)}
          />
          {<datalist id="Values">
            {values.map((v: string | number) => <option key={v} value={v}/>)}
          </datalist>}
        </div>
        <div className={'col-3'}>
          <button title="Add attribute with value" onClick={addAttrVal}>+</button>
        </div>
      </div>
      <div className={'row'} style={{display: 'none'}}>
        <div className={'col-12'}>
          <span className="error">{/* todo show error */}</span>
        </div>
      </div>
      <div className={'row'}>
        <div className={'col-12'}>
          <input type="submit" value="Create" disabled={isSubmitting && isDirty && isValid} data-testid="entity-form-submit-btn"/>
        </div>
      </div>
    </form>
  )
}

export default EntityForm