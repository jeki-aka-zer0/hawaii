import React, { useEffect, useState } from 'react'
import axios from 'axios'

type Entity = {
  readonly name: string
  readonly description: string
}

const EntitiesList: React.FC = () => {
  const [entities, setEntities] = useState<Entity[]>([])
  const [loading, setLoading] = useState<boolean>(true)
  const [currentPageUrl, setCurrentPageUrl] = useState<string>(`${process.env.REACT_APP_API_URL}/eav/entity`)
  const [prevPageUrl, setPrevPageUrl] = useState<string | null>(null)
  const [nextPageUrl, setNextPageUrl] = useState<string | null>(null)

  useEffect(() => {
    setLoading(true)
    const controller = new AbortController()
    axios
      .get(currentPageUrl, {
        signal: controller.signal
      })
      .then(res => {
        setLoading(false)
        setEntities(res.data.results)
        setPrevPageUrl(res.data.previous)
        setNextPageUrl(res.data.next)
      })
      .catch(error => console.log(error))

    return () => controller.abort()
  }, [currentPageUrl])

  function gotoPrevPage () {
    prevPageUrl && setCurrentPageUrl(prevPageUrl)
  }

  function gotoNextPage () {
    nextPageUrl && setCurrentPageUrl(nextPageUrl)
  }

  if (loading) {
    return (<span className="loader">Loading<span>.</span><span>.</span><span>.</span></span>)
  }

  return (
    <>
      {entities.map((e: Entity) => (
        <p key={e.name}><b>{e.name}</b>, {e.description}</p>
      ))}
      <div>
        {prevPageUrl && <button onClick={gotoPrevPage}>Previous</button>}
        {nextPageUrl && <button onClick={gotoNextPage}>Next</button>}
      </div>
    </>
  )
}

export default EntitiesList
