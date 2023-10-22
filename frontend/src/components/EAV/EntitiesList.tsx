import React, { useEffect, useState } from 'react'
import './EntitiesList.css'
import axios from 'axios'
import {Entity, ListResponse} from "../../types/types";
import Loader from "../Shared/Loader"
import { Link } from 'react-router-dom'

const EntitiesList: React.FC = () => {
  const [entities, setEntities] = useState<Entity[]>([])
  const [loading, setLoading] = useState<boolean>(true)
  const [currentPageUrl, setCurrentPageUrl] = useState<string>(`${process.env.REACT_APP_API_URL}/eav/entity`)
  const [prevPageUrl, setPrevPageUrl] = useState<string | null>(null)
  const [nextPageUrl, setNextPageUrl] = useState<string | null>(null)

  useEffect(() => {
    setLoading(true)
    const controller : AbortController = new AbortController()
    axios
      .get<ListResponse>(currentPageUrl, {
        signal: controller.signal
      })
      .then(res => {
        setLoading(false)
        setEntities(res.data.results)
        setPrevPageUrl(res.data.previous)
        setNextPageUrl(res.data.next)
      })
      .catch(error => console.log(error))

    return (): void => controller.abort()
  }, [currentPageUrl])

  function gotoPrevPage(): void {
    prevPageUrl && setCurrentPageUrl(prevPageUrl)
  }

  function gotoNextPage(): void {
    nextPageUrl && setCurrentPageUrl(nextPageUrl)
  }

  return loading
      ? <Loader/>
      : (
          <>
            {entities.map((e: Entity) => (
              <p key={e.name}><b>{e.name}</b>, {e.description}, <Link to={`/entity/${e.entity_id}`}>View</Link></p>
            ))}
            <div className="pager">
              {<button onClick={gotoPrevPage} disabled={!prevPageUrl}>←</button>}
              {<button onClick={gotoNextPage} disabled={!nextPageUrl}>→</button>}
            </div>
          </>
      )
}

export default EntitiesList
