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
    <h1 id="hello"></h1>
    <label for="article-id">Select Story</label>

<select id="article-id">
    @foreach ($data as $a)


  <option value="{{$a['id']}}">({{$a['id']}}) {{$a['article_title']}}</option>
  @endforeach
</select>
    {{-- <label for="article-id">Article ID:</label>
    <input type="text" id="article-id" placeholder="Enter Article ID"> --}}

    <button onclick="loadGraph()">Load Graph</button>
    <h2 id="title"></h2>
    <p id="paragraph" data-full="false"></p>
    <button id="show-more" onclick="showMoreParagraph()" style="display:none;" >Show More</button>

    <div id="chart"></div>

    <div class="mb-3" id="button1" style="display: none;">
        <button id="prev"  onclick="prevGraph()" disabled>Previous</button>
        <button id="next"  onclick="nextGraph()" disabled>Next</button>
    </div>

    <div class="rating-container1" id="status1" style="display:none">
        <input type="hidden" id="value">
        <input type="hidden" id="value2">
        <input type="hidden" id="value3">
        <label>Relevance: <input type="number" min="1" max="5" id="rating1"></label>
        <label>Clarity and Coherence: <input type="number" min="1" max="5" id="rating2"></label>
        <label>Visualization Quality: <input type="number" min="1" max="5" id="rating3"></label>
        <label>Narrative Quality: <input type="number" min="1" max="5" id="rating4"></label>
        <label>Factual Correctness: <input type="number" min="1" max="5" id="rating5"></label>
        <div class="submission-container">
            <button id="submit-btn" onclick="submitRating()">Submit</button>
        </div>
    </div>
    {{-- <div class="rating-container2" id="status2" style="display:none;">
        <p>Already submit for this story</p>
    </div> --}}

    <div class="error-message" style="display:none;">Please enter a value between 1 and 5.</div>

    <script>
        let currentIndex = 0;
        let currentData = [];

        function loadGraph() {
            const articleId = document.getElementById('article-id').value;
            fetch(`/data/${articleId}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data); // Print the fetched data
                    if (data.error) {
                        document.getElementById('chart').innerText = data.error;
                    } else {
                        const titleElement2 = document.getElementById('hello');
                        const titleElement4 = document.getElementById('value2');
                        const titleElement5 = document.getElementById('value3');
                        titleElement5.value = data.title;
                        titleElement2.innerText = data.title;
                        titleElement4.value = data.article_ids;
                        // if(data.status == true){
                            var div2 = document.getElementById('status1');
                        //     // var div3 = document.getElementById('status2');
                               div2.style.display = 'block';
                        //     //    div3.style.display = 'block';
                        // }else{
                        //     var div2 = document.getElementById('status1');
                        //     // var div3 = document.getElementById('status2');
                        //     //    div2.style.display = 'block';
                        //        div3.style.display = 'none';
                        // }
                        if (data.gpt_table_para_pair_agent && data.gpt_table_para_pair_agent.length > 0) {
                            currentData = [...data.gpt_table_para_pair_agent];
                            const titleElement3 = document.getElementById('value');
                            var div4 = document.getElementById('button1');
                            var div5 = document.getElementById('show-more');
                            div4.style.display = 'block';
                            div5.style.display = 'block';

                            titleElement3.value = "agent"
                        } else {
                            currentData = [...data.gpt_table_para_pair_noagent];
                            const titleElement3 = document.getElementById('value');
                            var div4 = document.getElementById('button1');
                            var div5 = document.getElementById('show-more');

                            div4.style.display = 'block';
                            div5.style.display = 'block';

                            titleElement3.value = "no-agent"
                        }
                        currentIndex = 0;
                        document.getElementById('prev').disabled = true;
                        document.getElementById('next').disabled = currentData.length <= 1;
                        displayGraph();
                        showTitleAndParagraph(currentData[0].title, currentData[0].paragraph);
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

        function submitRating() {
            const ratings = [
                document.getElementById('rating1').value,
                document.getElementById('rating2').value,
                document.getElementById('rating3').value,
                document.getElementById('rating4').value,
                document.getElementById('rating5').value
            ].map(Number);

            if (ratings.some(r => r < 1 || r > 5 || isNaN(r))) {
                document.querySelector('.error-message').style.display = 'block';
                return;
            }

            const articleId = document.getElementById('article-id').value;
            const ag = document.getElementById('value').value;
            const id = document.getElementById('value2').value;
            const title = document.getElementById('value3').value;

            fetch('/save-rating', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        article_id: articleId,
                        graph_index: ag,
                        article_ids: id,
                        title: title,
                        ratings: ratings
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Rating saved successfully');
                        window.location.href = '/';
                    } else {
                        alert('Error saving rating');
                    }
                })
                .catch(error => console.error('Error saving rating:', error));
        }
    </script>
</body>

</html>
