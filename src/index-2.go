package main

import (
    "fmt"
    "os"
//     "io/ioutil"
)

func check(e error) {
    if e != nil {
        panic(e)
    }
}

func main() {
    dir, err := os.Open("var/data")
    check(err)
    defer dir.Close()

    files, err := dir.Readdir(-1)
    check(err)

    for _, file := range files {
//         ioutil.ReadFile
        if file.IsDir() {
            continue
        }

        f, err := os.Open(file.Name())
        check(err)

        fmt.Println(file.Name())

        b1 := make([]byte, 5)
        n1, err := f.Read(b1)
        check(err)
        fmt.Printf("%d bytes: %s\n", n1, string(b1[:n1]))
    }
}