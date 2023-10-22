import React, {useEffect, FC, useState} from 'react'
import axios from "axios";
import {Entity} from "../../types/types";
import { useParams } from 'react-router-dom'

const EntityView: FC = () => {
    const [loading, setLoading] = useState<boolean>(true)
    let { entityId } = useParams<"entityId">();

    useEffect(() => {
        setLoading(true)
        const controller : AbortController = new AbortController()
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

        return (): void => controller.abort()
    })

    return (
        <div>{entityId}</div>
    )
}

export default EntityView