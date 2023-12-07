import React from 'react'
import {render, screen} from '@testing-library/react'
import EntityForm from './EntityForm'

jest.mock('react-router-dom', () => ({
    ...jest.requireActual('react-router-dom'),
    useNavigate: () => jest.fn(),
}));

describe('<EntityForm/>', () => {
    test('should display empty form', () => {
        render(<EntityForm/>)
        const form: HTMLElement = screen.getByTestId('entity-form')

        expect(form).toHaveFormValues({
            name: "",
            description: ""
        })
    })
})
