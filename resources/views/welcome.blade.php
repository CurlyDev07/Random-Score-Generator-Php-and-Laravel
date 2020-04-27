<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Score Generator</title>

    <link href="{{ asset('css\tailwind.min.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container mx-auto px-4">
        <h1 class="mt-5 font-medium text-xl">Score Generator</h1>
        <div class="relative">
            <canvas id="line-chart" style="height: 450px;"></canvas>
            <button id="generateScore" class="focus:bg-blue-500 focus:text-white absolute border focus:outline-none mr-32 -mt-4 px-2 py-1 right-0 rounded top-0">Add Data</button>
            <button id="day" data-value="day" class="update-chart focus:bg-blue-500 focus:text-white absolute border focus:outline-none mr-1 -mt-4 px-2 py-1 right-0 rounded top-0">Day</button>
            <button id="time" data-value="time" class="update-chart focus:bg-blue-500 focus:text-white absolute border focus:outline-none mr-16 -mt-4 px-2 py-1 right-0 rounded top-0">Time</button>
        </div>
    </div>

    <script src="{{ asset('js\chart.min.js') }}"></script>
    <script src="{{ asset('js\jquery.min.js') }}"></script>
    <script>

        $(document).ready(function () {
            var score = '';
            var score_generated_count = '';
            var day = '';
            
            var chart = new Chart(document.getElementById("line-chart"), {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        { 
                            data: [],
                            label: "",
                            borderColor: "#3e95cd",
                            fill: false
                        }
                    ]
                }
            });// CHART

            function setChartDataGeneratedScorePerTime(){
                $.ajax({
                    type: "GET",
                    url: "{{ url('/') }}/api/get-scores?view=TIME",
                    success: (res)=>{
                        data = res.data.map(item => {
                            let data = [];
                            data = item.score;
                            return data;
                        })
                    
                        labels = res.data.map(item => {
                            let labels = [];
                            labels = item.created_at;
                            return labels;
                        })

                        chart.data.labels = labels
                        chart.data.datasets[0].data = data
                        chart.data.datasets[0].label = "Score generated per time"
                        chart.update();
                    }
                });// LINE CHART FOR DAY
            }// Per Time

            function setChartDataGeneratedScorePerDay(){
                $.ajax({
                    type: "GET",
                    url: "{{ url('/') }}/api/get-scores?view=DAY",
                    success: (res)=>{
                        var data = res.data.map(item => {
                            let count = [];
                            count = parseInt(item.count);
                            return count;
                        })
                    
                        var labels = res.data.map(item => {
                            let labels = [];
                            labels = 'score '+item.score +' | '+ item.day+'/'+item.month;
                            return labels;
                        })

                        chart.data.labels = labels
                        chart.data.datasets[0].data = data
                        chart.data.datasets[0].label = "Number of times score generated per day"
                        chart.update();
                    }
                });// LINE CHART FOR DAY
            } // Per Day

            setChartDataGeneratedScorePerTime();// call

            $('.update-chart').click(function () {
                let value = $(this).data('value');
                if (value == 'day') {
                    return setChartDataGeneratedScorePerDay();// update chart with datas by day
                }
                setChartDataGeneratedScorePerTime();// update chart with datas by time
            });

            $('#generateScore').click(()=>{
                $('#day').fadeOut();

                $.ajax({
                    type: "POST",
                    url: "{{ url('/') }}/api/generate-score",
                    success: (res)=>{
                        // console.log(res);
                        setChartDataGeneratedScorePerTime();// update chart with datas by time
                        chart.data.labels.push(res.created_at);
                        chart.data.datasets[0].data.push(res.score);
                        chart.update();

                        $('#day').fadeIn();
                    }
                });// LINE CHART FOR DAY
            });
        });
    </script>
</body>
</html>