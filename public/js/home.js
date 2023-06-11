// Fetch book API for popular books
document.querySelectorAll('.book-card').forEach(book => {
    var isbn = book.dataset.isbn;
    fetch(`https://openlibrary.org/api/books?bibkeys=ISBN:${isbn}&jscmd=data&format=json`)
        .then(res => res.json())
        .then(openLibraryData => {
            var openLibraryBook = Object.values(openLibraryData)[0];

            // Set the cover image from Open Library API
            if (openLibraryBook.cover) {
                var img = book.querySelector('img.book-card-img');
                img.src =
                    openLibraryBook.cover.large ||
                    openLibraryBook.cover.medium ||
                    openLibraryBook.cover.small;
                img.alt = `cover image for the book ${openLibraryBook.title}`;
            }

            // Set the title from Open Library API
            book.querySelector('div.book-name').textContent = openLibraryBook.title;

            // Set the author from Open Library API
            book.querySelector('div.book-by > span').textContent = openLibraryBook.authors[0].name;

            // Fetch additional information from Google Books API
            fetch(`https://www.googleapis.com/books/v1/volumes?q=isbn:${isbn}`)
                .then(res => res.json())
                .then(googleData => {
                    if (googleData.items && googleData.items.length > 0) {
                        var googleBook = googleData.items[0].volumeInfo;

                        // Set the description from Google Books API
                        book.querySelector('div.book-description').textContent = googleBook.description;
                    }
                });
        });
});

