// Fetch book information
var book = document.querySelector('.book_info')
var isbn = book.dataset.isbn;
fetch(`https://openlibrary.org/api/books?bibkeys=ISBN:${isbn}&jscmd=data&format=json`)
    .then(res => res.json())
    .then(data => {
        data = Object.values(data)[0];
        // set the cover image
        if (data.cover) {
            book_cover = book.querySelector('.book_photo');
            book_cover.src =
                data.cover.large
                || data.cover.medium
                || data.cover.small;
            book_cover.alt = `cover image for the book ${data.title}`;
        }

        // set the title
        book.querySelector('.book_title').textContent = data.title;

        // set author
        book.querySelector('.book_author').textContent = 'By ' + data.authors[0].name;
    });

