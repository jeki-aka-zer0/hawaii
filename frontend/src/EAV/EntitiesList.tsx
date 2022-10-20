import React, { useEffect, useState } from 'react'
import axios from 'axios'

type Entity = {
  readonly name: string
  readonly description: string
}

const EntitiesList: React.FC = () => {
  const [entities, setEntities] = useState<Entity[]>([])
  const [loading, setLoading] = useState<boolean>(true)

  useEffect(() => {
    setLoading(true)
    const controller = new AbortController()
    axios.get('http://localhost:8080/eav/entity', {
      signal: controller.signal
    }).then(res => {
      setLoading(false)
      setEntities(res.data.results)
    })

    return () => controller.abort()
  }, [])

  if (loading) {
    return (<p>Loading...</p>)
  }

  return (
    <>
      {entities.map((e: Entity) => (
        <p key={e.name}><b>{e.name}</b>, {e.description}</p>
      ))}
    </>
  )
}

export default EntitiesList
