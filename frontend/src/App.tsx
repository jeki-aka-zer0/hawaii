import React from 'react'
import './App.css'
import EntitiesList from './EAV/EntitiesList'
import { Link, Route, Routes } from 'react-router-dom'
import EntityForm from './EAV/EntityForm'

function App () {
  return (
    <>
      <header>
        <nav>
          <ul>
            <li><Link to="/">Home</Link></li>
            <li>
              <Link to="/entities">Entities</Link>
              <ul>
                <li><Link to="/entities/create">Create</Link></li>
              </ul>
            </li>
          </ul>
        </nav>
      </header>
      <main>
        <Routes>
          <Route path="/" element={<h1>Welcome to Hawaii</h1>}/>
          <Route path="/entities">
            <Route index element={<EntitiesList/>}/>
            <Route path="create" element={<EntityForm/>}/>
          </Route>
          <Route path="*" element={<h1>Page not found</h1>}/>
        </Routes>
      </main>
    </>
  )
}

export default App
