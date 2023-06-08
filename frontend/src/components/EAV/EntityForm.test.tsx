import React from 'react'
import { render, screen } from '@testing-library/react'
import EntityForm from './EntityForm'

describe('<EntityForm/>', () => {
  test('should display empty form', () => {
    render(<EntityForm/>)
    const form = screen.getByTestId('entity-form')

    expect(form).toHaveFormValues({
      name: "",
      description: ""
    })
  })
})
