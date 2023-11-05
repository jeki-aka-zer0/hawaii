import React, { useEffect, FC, useState, useRef } from 'react'
import axios from "axios";
import {Entity} from "../../types/types";
import { useParams } from 'react-router-dom'

const EntityView: FC = () => {
    const [loading, setLoading] = useState<boolean>(true)
    let { entityId } = useParams<"entityId">();
    const effectRun = useRef(false);

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
                  console.log(res)
                  // setEntity(res.data.results)
              })
              .catch(error => console.log(error))
        }

        return (): void => {
            controller.abort()
            effectRun.current = true;
        }
    }, [])

    return (
        <div>{entityId}</div>
    )
}

export default EntityView