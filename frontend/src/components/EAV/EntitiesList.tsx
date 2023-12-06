import React, { useEffect, useRef, useState } from 'react'
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
  const effectRun:React.MutableRefObject<boolean> = useRef(false);

  useEffect(() => {
    setLoading(true)
    const controller : AbortController = new AbortController()
    if (effectRun.current) {
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
    }

    return (): void => {
      controller.abort()
      effectRun.current = true;
    }
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
          entities?.length ?
              <>
                {entities.map((e: Entity) => (
                    <div key={e.name} className={"entity-card"}>
                      <div className={"entity-card__header"}>
                        <h2 className={"entity-card__name"}>{e.name}</h2>
                        <Link className={"entity-card__btn-view"} to={`/entity/${e.entity_id}`}>View</Link>
                        {e.attributes_values.map(av =>
                            <span className={"tag"} title={av.name} key={av.name + av.value}>{av.value}</span>)}
                      </div>
                      <p>{e.description}</p>
                    </div>
                ))}
                {(prevPageUrl || nextPageUrl) && <div className="pager">
                  {<button onClick={gotoPrevPage} disabled={!prevPageUrl}>←</button>}
                  {<button onClick={gotoNextPage} disabled={!nextPageUrl}>→</button>}
                </div>}
              </>
              : <p>There are no entities yet.</p>
      )
}

export default EntitiesList
