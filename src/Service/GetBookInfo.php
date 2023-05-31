<?php

namespace App\Service;

class GetBookInfo
{
    public function getBookCover(string $isbn): string
    {
        $url = 'https://covers.openlibrary.org/b/isbn/'.$isbn.'-L.jpg';

        // Check if the image is too small
        $imageSize = @getimagesize($url);
        if (false !== $imageSize && ($imageSize[0] < 200 || $imageSize[1] < 200)) {
            // Return a blank image URL
            $url = 'https://ps.w.org/replace-broken-images/assets/icon-256x256.png?rev=2561727';
        }

        return $url;
    }

    public function getBookTitle(string $isbn): string
    {
        $url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:'.$isbn.'&key=AIzaSyBE4Jpq7yFvTEZtgcX4ONKRz0ZMbIt397w';
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        if (empty($data['items'][0]['volumeInfo']['title'])) {
            return 'No title';
        }

        return $data['items'][0]['volumeInfo']['title'];
    }

    public function getBookAuthor(string $isbn): string
    {
        $url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:'.$isbn.'&key=AIzaSyBE4Jpq7yFvTEZtgcX4ONKRz0ZMbIt397w';
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        if (empty($data['items'][0]['volumeInfo']['authors'][0])) {
            return 'No author';
        }

        return $data['items'][0]['volumeInfo']['authors'][0];
    }

    public function getBookRate(string $isbn): string
    {
        $url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:'.$isbn.'&key=AIzaSyBE4Jpq7yFvTEZtgcX4ONKRz0ZMbIt397w';
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        if (empty($data['items'][0]['volumeInfo']['averageRating'])) {
            return 'No rate';
        }

        return $data['items'][0]['volumeInfo']['averageRating'];
    }

    public function getBookSummary(string $isbn): string
    {
        $url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:'.$isbn.'&key=AIzaSyBE4Jpq7yFvTEZtgcX4ONKRz0ZMbIt397w';
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        if (empty($data['items'][0]['volumeInfo']['description'])) {
            return 'No summary';
        }

        return $data['items'][0]['volumeInfo']['description'];
    }
}
