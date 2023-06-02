// Fetch book API for sliding books
document
  .querySelectorAll('.book-slide .book-cell')
  .forEach(book => {
    var isbn = book.dataset.isbn;
    fetch(`https://openlibrary.org/api/books?bibkeys=ISBN:${isbn}&jscmd=data&format=json`)
      .then(res => res.json())
      .then(data => {
        data = Object.values(data)[0];
        // set the cover image
        if (data.cover) {
          book_photo = book.querySelector('img.book-photo');
          book_photo.src =
            data.cover.large
            || data.cover.medium
            || data.cover.small;
          book_photo.alt = `cover image for the book ${data.title}`;
        }

        // set the title
        book.querySelector('div.book-title').textContent = data.title;

        // set author
        book.querySelector('div.book-author > span').textContent
          = data.authors[0].name;
      });
  });

// Fetch book API for books of the year
document
  .querySelectorAll('.books-of > .week.year > .year-book')
  .forEach(book => {
    var isbn = book.dataset.isbn;
    fetch(`https://openlibrary.org/api/books?bibkeys=ISBN:${isbn}&jscmd=data&format=json`)
      .then(res => res.json())
      .then(data => {
        data = Object.values(data)[0];
        // set the cover image
        if (data.cover) {
          img = book.querySelector('img.year-book-img');
          img.src =
            data.cover.large
            || data.cover.medium
            || data.cover.small;
          img.alt = `cover image for the book ${data.title}`;
        }

        // set the title
        book.querySelector('div.year-book-name').textContent = data.title;

        // set author
        book.querySelector('div.year-book-author > span').textContent
          = data.authors[0].name;
      });
  });

// Fetch book API for popular books
document
  .querySelectorAll('.popular-books .book-card')
  .forEach(book => {
    var isbn = book.dataset.isbn;
    fetch(`https://openlibrary.org/api/books?bibkeys=ISBN:${isbn}&jscmd=data&format=json`)
      .then(res => res.json())
      .then(data => {
        data = Object.values(data)[0];
        // set the cover image
        if (data.cover) {
          img = book.querySelector('img.book-card-img');
          img.src =
            data.cover.large
            || data.cover.medium
            || data.cover.small;
          img.alt = `cover image for the book ${data.title}`;
        }

        // set the title
        book.querySelector('div.book-name').textContent = data.title;

        // set author
        book.querySelector('div.book-by > span').textContent
          = data.authors[0].name;
      });
  });
