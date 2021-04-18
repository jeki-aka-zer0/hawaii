package main

import (
    "fmt"
    "os"
    "path/filepath"
//     s "strings"
    "io"
)

const dir string = "var/data"

func main() {
    for _, path := range readHeicImages() {
//         fmt.Println(path)

        file, err := os.Open(path)
        check(err)
        defer file.Close()

        header := make([]byte, 4)
        io.ReadFull(file, header)
        check(err)

        fmt.Println(string(header))

//         switch string(header) {
//         case "II*\x00":
//             fmt.Println("1")
//         case "Exif":
//             fmt.Println(file)
//         default:
//             fmt.Println("no")
//         }
    }
}

func readHeicImages() []string {
    var images []string

    err := filepath.Walk(dir, func(path string, info os.FileInfo, err error) error {
        check(err)

//         if (!info.IsDir() && s.ToLower(filepath.Ext(path)) == ".heic") {
            images = append(images, path)
//         }

        return nil
    })

    check(err)

    return images
}

func check(e error) {
    if e != nil {
        panic(e)
    }
}
