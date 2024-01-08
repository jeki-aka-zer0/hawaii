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
  const [searchParams] = useSearchParams()
  const [isDelayOn, setIsDelayOn] = useState<boolean>(false)
  const effectRun:React.MutableRefObject<boolean> = useRef(false)
  const navigate: NavigateFunction = useNavigate()

  useEffect(() => {
    const controller : AbortController = new AbortController()
    const timeout = setTimeout((): void => {
      setLoading(true)
      if (effectRun.current) {
        setIsDelayOn(false) // reset delay for the pagination
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
    }, isDelayOn ? 500 : 0);

    return (): void => {
      controller.abort()
      effectRun.current = true
      clearTimeout(timeout)
    }
  }, [searchParams])

  function goto(page: number | null): void {
    if (page === null) {
      return
    }
    searchParams.set("offset", page.toString())
    navigate(`/entities?${searchParams.toString()}`)
  }

  function search(e: React.ChangeEvent<HTMLInputElement>): void {
    setIsDelayOn(true) // turn on delay to allow type the searching phrase
    searchParams.set("search", e.target.value)
    searchParams.delete("offset")
    navigate(`/entities?${searchParams.toString()}`)
  }

  return loading
      ? <Loader/>
      : <>
        <div className={'row'}>
          <div className={'col-6'}>
            <label htmlFor="search">Search</label>
            <input type="text"
                   id={"search"}
                   onChange={search}
                   value={searchParams.get("search") ?? ""}
                   autoFocus={Boolean(searchParams.get("search"))}
            />
          </div>
          <div className={'col-6'} defaultValue={3}>
            <label htmlFor="limit">Per page</label>
            <select name="limit" id={"limit"} className={"select"}>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
            </select>
          </div>
        </div>
        {entities?.length
            ? <>
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
            : <p>No entities.</p>}
      </>
}

export default EntitiesList
