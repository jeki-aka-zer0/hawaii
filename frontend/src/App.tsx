import React from 'react'
import './App.css'
import EntitiesList from './components/EAV/EntitiesList'
import { Link, Route, Routes } from 'react-router-dom'
import EntityForm from './components/EAV/EntityForm'

function App () {
  return (
    <>
      <header className={'content'}>
        <nav>
          <ul>
            <li><Link to="/">Home</Link></li>
            <li>
              <Link to="/entities">Entities</Link>
            </li>
            <li>
              <Link to="/entities/create">Create</Link>
            </li>
          </ul>
        </nav>
      </header>
      <main className={'content'}>
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
