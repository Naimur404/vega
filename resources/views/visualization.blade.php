<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vega-Lite Graphs</title>
    <script src="https://cdn.jsdelivr.net/npm/vega@5"></script>
    <script src="https://cdn.jsdelivr.net/npm/vega-lite@5"></script>
    <script src="https://cdn.jsdelivr.net/npm/vega-embed@6"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f4f4f4;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 30px;
        }

        h1 {
            color: #007bff;
            margin-bottom: 30px;
        }

        h2 {
            color: #555;
            margin-top: 20px;
        }

        .chart-container {
            width: 100%;
            max-width: 1200px;
            /* Maximum width for the container */
            margin: 0 auto;
            /* Center the container */
            overflow: hidden;
        }

        .vega-embed {
            width: 100%;
            height: auto;
        }

        .chart-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        canvas {
            /* Ensure the canvas doesn't exceed the container's width on smaller screens */
            width: 100% !important;
            height: auto !important;
            max-width: 100%;
        }

        @media (min-width: 1200px) {
            .chart-wrapper {
                justify-content: flex-start;
                /* Align canvas to the left on large screens */
            }

            canvas {
                width: auto !important;
                /* Display canvas at its original size */
                max-width: none;
                /* Remove max-width restriction on large screens */
            }
        }

        .btn-sm {
            margin-right: 10px;
        }

        #paragraph {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .rating-container1 {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .error-message {
            color: #dc3545;
            margin-top: 10px;
        }

        #button1 {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <main>
        <div class="container mt-3">
            <h1 id="hello"></h1>
            <label for="article-id">Select Story</label>

            <select id="article-id" class="form-select" aria-label="Default select example">
                @foreach ($data as $a)
                    <option value="{{ $a['id'] }}">({{ $a['id'] }}) {{ $a['article_title'] }}</option>
                @endforeach
            </select>

            <button onclick="loadGraph()" class="btn btn-primary mt-2 btn-sm">Load Graph</button>
            <h2 id="title" class="mt-2"></h2>
            <p id="paragraph" data-full="false" style="display:none"></p>
            <button id="show-more" onclick="showMoreParagraph()" style="display:none;"
                class="btn btn-success btn-sm">Show More</button>

            <div class="row chart-container mt-2">
                <div id="chart"></div>

            </div>
            <div class="mb-3" id="button1" style="display: none;">
                <button id="prev" onclick="prevGraph()" disabled class="btn btn-danger btn-sm">Previous</button>
                <button id="next" onclick="nextGraph()" disabled class="btn btn-warning btn-sm">Next</button>
            </div>


            <div class="container mt-5" id="container1" style="display:none">
                <div class="rating-container1" id="status1" style="display:none">
                    <input type="hidden" id="value">
                    <input type="hidden" id="value2">
                    <input type="hidden" id="value3">

                    <div class="row mb-3">
                        <div class="col">
                            <label for="rating1" class="form-label">Relevance:</label>
                            <input type="number" min="1" max="5" id="rating1" class="form-control">
                        </div>
                        <div class="col">
                            <label for="rating2" class="form-label">Clarity:</label>
                            <input type="number" min="1" max="5" id="rating2" class="form-control">
                        </div>
                        <div class="col">
                            <label for="rating3" class="form-label">Visualization:</label>
                            <input type="number" min="1" max="5" id="rating3" class="form-control">
                        </div>
                        <div class="col">
                            <label for="rating4" class="form-label">Narrative:</label>
                            <input type="number" min="1" max="5" id="rating4" class="form-control">
                        </div>
                        <div class="col">
                            <label for="rating5" class="form-label">Factual:</label>
                            <input type="number" min="1" max="5" id="rating5" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-center">
                            <button id="submit-btn" onclick="submitRating()" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="error-message" style="display:none;">Please enter a value between 1 and 5.</div>
        </div>
    </main>

    <script>
        let currentIndex = 0;
        let currentData = [];

        function loadGraph() {
            const articleId = document.getElementById('article-id').value;
            fetch(`/data/${articleId}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.error) {
                        document.getElementById('chart').innerText = data.error;
                    } else {
                        const titleElement2 = document.getElementById('hello');
                        const titleElement4 = document.getElementById('value2');
                        const titleElement5 = document.getElementById('value3');
                        var div6 = document.getElementById('paragraph');
                        var div7 = document.getElementById('container1');
                        div6.style.display = 'block';
                        div7.style.display = 'block';
                        titleElement5.value = data.title;
                        titleElement2.innerText = data.title;
                        titleElement4.value = data.article_ids;

                        var div2 = document.getElementById('status1');
                        div2.style.display = 'block';

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

                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Please enter a value between 1 and 5.",
                    //   footer: '<a href="#">Why do I have this issue?</a>'
                });
                // document.querySelector('.error-message').style.display = 'block';
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Submit it"
            }).then((result) => {
                if (result.isConfirmed) {
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
                                // alert('Rating saved successfully');
                                window.location.href = '/';
                            } else {
                                alert('Error saving rating');
                            }
                        })
                        .catch(error => console.error('Error saving rating:', error));
                }
            });


        }
    </script>
</body>

</html>
