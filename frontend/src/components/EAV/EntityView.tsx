import React, { useEffect, FC, useState, useRef } from 'react'
import axios from "axios"
import {Entity} from "../../types/types"
import { useParams } from 'react-router-dom'
import Loader from '../Shared/Loader'
import Contenteditable from "../Shared/Contenteditable";

const EntityView: FC = () => {
    const [loading, setLoading] = useState<boolean>(true)
    const [entity, setEntity] = useState<Entity>()
    let { entityId } = useParams<"entityId">()
    const effectRun = useRef(false)

    useEffect(() => {
        setLoading(true)
        const controller : AbortController = new AbortController()
        if (effectRun.current) {
            axios
              .get<Entity>(`${process.env.REACT_APP_API_URL}/eav/entity/${entityId}`, {
                  signal: controller.signal
              })
              .then(res => {
                  setLoading(false)
                  setEntity(res.data)
              })
              .catch(error => console.log(error))
        }

        return (): void => {
            controller.abort()
            effectRun.current = true
        }
    }, [entityId])

    return loading
      ? <Loader/>
      : (
        <div>
            <h1>{entity?.name}</h1>
            <p>
                {entity?.attributes_values.map(av => <span className={"tag"} title={av.name} key={av.name + av.value}>{av.value}</span>)}
            </p>
            <Contenteditable value={entity?.description ?? ""} onChange={(text: string):void => {
                let entityNew: (Entity | undefined) = entity
                if (entityNew) {
                    entityNew.description = text
                    setEntity(entityNew)
                }
            }}/>
        </div>
    )
}

export default EntityView