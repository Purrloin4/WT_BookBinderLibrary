const bookItems = document.getElementsByClassName('bookItem')
const infoItem = document.getElementByClassName('info-text')
const isbn13s = {{ isbn13s|json_encode|raw }};

for (const book of bookItems) {
    book.addEventListener('click', updateReference)
}

function updateReference(event) {
    let isbn = event.target.dataset.isbn
    fetch(`https://openlibrary.org/isbn/${isbn}.json`)
        .then((response) => {
            if (response.ok) return response.json()
            else throw new Error('not found')
        })
        .then((result) => {
            console.log(result)
            infoItem.innerHTML = `published on ${result.publish_date} (${result.number_of_pages} pages)`
        })
        .catch(error => infoItem.innerHTML = "No extra info found")
}


for (const isbn13 of isbn13s) {
    fetchBookInformation(isbn13);
}

function fetchBookInformation(isbn13) {
    fetch(`https://openlibrary.org/isbn/${isbn13}.json`)
        .then(response => response.ok ? response.json() : Promise.reject('not found'))
        .then(bookData => {
            const bookCard = document.querySelector(`.book-card[data-isbn="${isbn13}"]`);
            if (bookCard) {
                const bookNameElement = bookCard.querySelector('.book-name');
                const bookAuthorElement = bookCard.querySelector('.book-by');
                const bookRateElement = bookCard.querySelector('.rate');
                const bookSumElement = bookCard.querySelector('.book-sum');

                bookNameElement.textContent = bookData.title ?? '';
                bookAuthorElement.textContent = bookData.authors ? bookData.authors[0].name : '';
                bookRateElement.textContent = bookData.rating?.average ?? '';
                bookSumElement.textContent = bookData.description?.value ?? '';
            }
        })
        .catch(error => console.error(error));
}