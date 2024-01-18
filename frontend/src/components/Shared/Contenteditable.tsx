import React, {JSX, useEffect, useRef} from "react";

export default function Contenteditable(props: { value: string, onChange: Function }): JSX.Element {
    const contentEditableRef: React.RefObject<HTMLDivElement> = useRef<HTMLDivElement>(null)

    useEffect((): void => {
        if (contentEditableRef.current != null && contentEditableRef.current.textContent !== props.value) {
            contentEditableRef.current.textContent = props.value
        }
    })

    return (
        <div
            contentEditable="true"
            ref={contentEditableRef}
            onInput={(e: React.FormEvent<HTMLInputElement>): void => {
                props.onChange(e.currentTarget.innerText);
            }}
        />
    );
}