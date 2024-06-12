<!-- resources/views/visualization.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vega-Lite Graphs</title>
    <script src="https://cdn.jsdelivr.net/npm/vega@5"></script>
    <script src="https://cdn.jsdelivr.net/npm/vega-lite@5"></script>
    <script src="https://cdn.jsdelivr.net/npm/vega-embed@6"></script>
    <style>
        #chart {
            width: 100%;
            height: 500px;
        }
    </style>
</head>
<body>
    <h1>Select Article ID and Key</h1>
    <label for="article-id">Article ID:</label>
    <input type="text" id="article-id" placeholder="Enter Article ID">

    <label for="key">Select Key:</label>
    <select id="key">
        <option value="gpt_table_para_pair_agent">Agent</option>
        <option value="gpt_table_para_pair_noagent">No Agent</option>
    </select>

    <button onclick="loadGraph()">Load Graph</button>
    <h2 id="title"></h2>
    <p id="paragraph" data-full="false"></p>
    <button id="show-more" onclick="showMoreParagraph()">Show More</button>


    <div id="chart"></div>

    <div class="mb-3">
        <button id="prev" onclick="prevGraph()" disabled>Previous</button>
        <button id="next" onclick="nextGraph()" disabled>Next</button>
    </div>
    <div class="rating-container">
        <label>Rating 1: <input type="number" min="1" max="5" placeholder="1"></label>
        <label>Rating 2: <input type="number" min="1" max="5" placeholder="2"></label>
        <label>Rating 3: <input type="number" min="1" max="5" placeholder="3"></label>
        <label>Rating 4: <input type="number" min="1" max="5" placeholder="4"></label>
        <label>Rating 5: <input type="number" min="1" max="5" placeholder="5"></label>
    </div>
    <div class="submission-container">
    <button id="submit-btn">Submit</button>
    </div>
    <div class="error-message">Please enter a value between 1 and 5.</div>
    <script>
        let currentIndex = 0;
        let currentData = [];

        function loadGraph() {
            const articleId = document.getElementById('article-id').value;
            const key = document.getElementById('key').value;
            fetch(`/data/${articleId}/${key}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data); // Print the fetched data
                    if (data.error) {
                        document.getElementById('chart').innerText = data.error;
                    } else {
                        currentData = data;
                        currentIndex = 0;
                        document.getElementById('prev').disabled = true;
                        document.getElementById('next').disabled = currentData.length <= 1;
                        displayGraph();
                        showTitleAndParagraph(data[0].title, data[0].paragraph);
                    }
                })
                .catch(error => console.error('Error loading graph:', error));
        }

        function displayGraph() {
            if (currentData.length > 0) {
                const spec = currentData[currentIndex].vegalite_spec;
                if (spec) {
                    const parsedSpec = JSON.parse(spec);
                    vegaEmbed('#chart', parsedSpec).catch(console.error);
                } else {
                    document.getElementById('chart').innerText = 'No graph available.';
                }
            }
        }

        function prevGraph() {
            if (currentIndex > 0) {
                currentIndex--;
                document.getElementById('next').disabled = false;
                if (currentIndex === 0) {
                    document.getElementById('prev').disabled = true;
                }
                displayGraph();
                showTitleAndParagraph(currentData[currentIndex].title, currentData[currentIndex].paragraph);
            }
        }

        function nextGraph() {
            if (currentIndex < currentData.length - 1) {
                currentIndex++;
                document.getElementById('prev').disabled = false;
                if (currentIndex === currentData.length - 1) {
                    document.getElementById('next').disabled = true;
                }
                displayGraph();
                showTitleAndParagraph(currentData[currentIndex].title, currentData[currentIndex].paragraph);
            }
        }

        function showMoreParagraph() {
            const paragraphElement = document.getElementById('paragraph');
            const showMoreButton = document.getElementById('show-more');
            if (paragraphElement.dataset.full === 'false') {
                paragraphElement.innerText = currentData[currentIndex].paragraph;
                showMoreButton.innerText = 'Show Less';
                paragraphElement.dataset.full = 'true';
            } else {
                paragraphElement.innerText = currentData[currentIndex].paragraph.substring(0, 200) + '...';
                showMoreButton.innerText = 'Show More';
                paragraphElement.dataset.full = 'false';
            }
        }

        function showTitleAndParagraph(title, paragraph) {
            const titleElement = document.getElementById('title');
            const paragraphElement = document.getElementById('paragraph');
            titleElement.innerText = title;
            paragraphElement.innerText = paragraph.substring(0, 200) + '...';
            paragraphElement.dataset.full = 'false';
        }
    </script>
</body>
</html>






