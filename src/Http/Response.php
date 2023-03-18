<?php

namespace Core\Http;

class Response
{
    protected ?string $body; // property to hold the response body as string type, nullable
    protected int $statusCode; // property to hold the HTTP status code as an integer type
    protected array $headers = []; // property to hold the list of headers as an associative array

    /**
     * Set the value of statusCode.
     *
     * @param   int  $statusCode  The HTTP status code.
     *
     * @return  response
     */
    public function setStatusCode($statusCode): Self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Set the value of body.
     *
     * @param   mixed  $body   The content of the response.
     *
     * @return  response
     */
    public function setBody($body): Self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set the value of headers.
     *
     * @param   string  $name   Header name.
     * @param   string  $value  Header value.
     *
     * @return  response
     */
    public function setHeaders($name, $value): Self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Set the response content with headers for JSON format.
     *
     * @param   mixed  $data   Array, object or scalar value to be converted to JSON string.
     *
     * @return  response
     */
    public function json($data): Self
    {
        $this->setHeaders('Content-Type',  'application/json');
        $this->setBody(json_encode($data));
        return $this;
    }

    /**
     * Set the response content for HTML format.
     *
     * @param   string  $view  Template path name.
     * @param   array   $data  Data to be passed to the view.
     *
     * @return  response
     */
    public function view($view, $data = []): Self
    {
        $this->setHeaders('Content-Type', 'text/html');
        $this->setBody(view($view, $data));
        return $this;
    }

    /**
     * Set the response content for HTML format.
     *
     * @param   string  $view  Template path name.
     * @param   array   $data  Data to be passed to the view.
     *
     * @return  response
     */
    public function viewError($data = []): Self
    {
        $this->setHeaders('Content-Type', 'text/html');
        $this->setBody(view('errors.index', $data));
        return $this;
    }

    /**
     * Send the HTTP response to the browser/client.
     *
     * @return  void
     */
    public function send(): void
    {
        http_response_code($this->statusCode); // set the HTTP response code to the specified value
        foreach ($this->headers as $key => $value) // loop through each header and assign it in the web server environment variable
            header("$key: $value");
        if (!empty($this->body)) { // check if the response body contains a non-empty string value before sending it to the output buffer
            echo $this->body;
        }
    }
}
