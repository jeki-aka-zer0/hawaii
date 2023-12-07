import React from 'react'
import {render, screen} from '@testing-library/react'
import App from './App'
import {BrowserRouter} from 'react-router-dom'

test('renders learn react link', (): void => {
    render(<BrowserRouter><App/></BrowserRouter>)
    const greeting: HTMLElement = screen.getByText(/Welcome to Hawaii/i)
    expect(greeting).toBeInTheDocument()
})
