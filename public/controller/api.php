<?php

class News_Today_API{
    
    public function news_today_request() {
        $url = 'https://newsapi.org/v2/everything?q=bitcoin&apiKey=a2300b13888b4540bfea189016ae0a32';
        
        // we will fetch the news api with wp_remote_get() function
        $response = wp_remote_get( $url, array(
            'timeout'     => 120,
		    'httpversion' => '1.1',
            'method' => 'GET',
        )); 

        // then check if the response is an error and return the error message 
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message"; 
        }

        // we will extract the body from the response with the wp_remote_retrieve_body()
        $body = wp_remote_retrieve_body( $response );
        
        // we will decode the json data with json_decode function
        // because it is easy for php to manipulate the data when it array
        $data = json_decode( $body, true );
        

        // we will make sure if the data is not empty and return the data
        if ( isset( $data ) && !empty( $data ) ) {
            return $data;
        } else {
            return false;
        }
    }

    public function news_today_response(){
        $data = $this->news_today_request();

        // if the data is not empty we call the display_news function below
        if ( $data ) {
            $this->display_news( $data );
            return $data;
        } else {
            echo 'Error fetching news.';
        }
        
    }

   public function display_news( $data ) {
    // define a string that will contain all the ui code 
    // since we are working with shortcode we will put it here in the output variable 
    // and return the string
    $output = '';

    if ( isset( $data['articles'] ) && !empty( $data['articles'] ) ) {
        $categories = array();
        
        // we group articles by category
        foreach ( $data['articles'] as $article ) {
            $category = isset( $article['source']['name'] ) ? $article['source']['name'] : 'Uncategorized';
            $categories[$category][] = $article;
        }

        // we display articles by category
        foreach ( $categories as $category => $articles ) {
            $output .= '<h2 class="category-title">' . $category . '</h2>';
            $output .= '<div class="news-card-container">';

            foreach ( $articles as $article ) {
                $output .= '<div class="news-card">';
                $output .= '<img src="' . esc_url( $article['urlToImage'] ) . '" alt="' . esc_attr( $article['title'] ) . '" class="news-image" />';
                $output .= '<div class="news-content">';
                $output .= '<h3 class="news-title">' . $article['title'] . '</h3>';
                $output .= '<p class="news-description">' . $article['description'] . '</p>';
                $output .= '<a href="' . esc_url( $article['url'] ) . '" target="_blank" class="news-link">Read more</a>';
                $output .= '</div></div>';
            }

            $output .= '</div>'; 
        }
    // if there is no article just simply return a string message
    } else {
        $output .= 'No articles found.'; 
    }

    return $output;
}
}