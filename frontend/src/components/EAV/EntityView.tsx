import React, {useEffect, FC, useState} from 'react'
import axios from "axios/index";
import {Entity} from "../../types/types";
import {useParams} from "react-router-dom";

const EntityView: FC<{entityId:string}> = ({entityId}) => {
    const [loading, setLoading] = useState<boolean>(true)
    useParams()

    useEffect(() => {
        setLoading(true)
        const controller : AbortController = new AbortController()
        axios
            .get<Entity>(`${process.env.REACT_APP_API_URL}/eav/entity/${entityId}`, {
                signal: controller.signal
            })
            .then(res => {
                setLoading(false)
                console.log()
                // setEntity(res.data.results)
            })
            .catch(error => console.log(error))

        return (): void => controller.abort()
    })

    return (
        <div>

        </div>
    )
}

export default EntityView