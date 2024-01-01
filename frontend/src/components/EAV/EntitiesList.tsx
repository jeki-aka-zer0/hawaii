import React, { useEffect, useRef, useState } from 'react'
import './EntitiesList.css'
import axios from 'axios'
import {Entity, ListResponse} from "../../types/types"
import Loader from "../Shared/Loader"
import { Link, NavigateFunction, useNavigate, useSearchParams } from 'react-router-dom'

const EntitiesList: React.FC = () => {
  const [entities, setEntities] = useState<Entity[]>([])
  const [loading, setLoading] = useState<boolean>(true)
  const [prevPageUrl, setPrevPageUrl] = useState<number | null>(0)
  const [nextPageUrl, setNextPageUrl] = useState<number | null>(0)
  const effectRun:React.MutableRefObject<boolean> = useRef(false)
  const navigate: NavigateFunction = useNavigate()
  const [searchParams] = useSearchParams()

  useEffect(() => {
    setLoading(true)
    const controller : AbortController = new AbortController()
    if (effectRun.current) {
      axios
        .get<ListResponse>(`${process.env.REACT_APP_API_URL}/eav/entity?${searchParams.toString()}`, {
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
      effectRun.current = true
    }
  }, [searchParams])

  function goto(page: number | null): void {
    if (page === null) {
      return
    }
    searchParams.set("offset", page.toString())
    navigate(`/entities?${searchParams.toString()}`)
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
                {(prevPageUrl !== null || nextPageUrl !== null) && <div className="pager">
                  {<button onClick={() => goto(prevPageUrl)} disabled={prevPageUrl == null}>←</button>}
                  {<button onClick={() => goto(nextPageUrl)} disabled={nextPageUrl == null}>→</button>}
                </div>}
              </>
              : <p>There are no entities yet.</p>
      )
}

export default EntitiesList
