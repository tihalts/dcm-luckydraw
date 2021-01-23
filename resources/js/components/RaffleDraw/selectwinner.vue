<template>
    

        <div style="height:100vh;">
            <div class="row h-100 justify-content-center align-items-center" v-if="!this.winner_selected">
                 <div class="col-sm-12 col-md-6">
                     <div class="ld-main-container"  style="background:transparent;">
                        <!-- <div style="background: url(../img/winner/1.jpg) top no-repeat;" class="card-top"></div> -->
                        <div class="ld-body">
                            <div class="ld-container">
                                <div v-for="(char , index) in strings" :key="index">{{ char }}</div>
                            </div>
                            <div class="ld-footer">
                                <button class="btn btn-danger btn-lg " v-if="!winnerselected" @click="stop">Stop</button>
                                <button class="btn btn-success btn-lg ml-3" v-if="!winnerselected" @click="start">Start</button>
                                <button class="btn btn-info btn-lg ml-3" v-if="winnerselected" @click="next">Next</button>
                            </div>
                        </div>
                        <!-- <div style="background: url(../img/winner/3.jpg) top no-repeat;" class="card-top"></div> -->
                    </div>
                 </div>
            </div>
            <div class="winner_selected" v-if="this.winner_selected" style="margin-top: 50px;">
                <div class="widget widget-user-card" style="padding:30px;padding: 30px;margin: auto;width:90%;max-width: 600px;">
                    <div class="widget-user-card__content" style="margin-top: 0px;">
                        <img src="img/full-logo-dark.png" alt="" class="widget-user-card__avatar">

                        <h1>{{ lucky_draw.name }}</h1>
                        <div>Campaign period: {{ lucky_draw.start_at | date}} to {{ lucky_draw.end_at | date}}</div><br/>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>                            
                                    <tr>
                                        <th>Raffle Draw Date :</th>
                                        <td>{{ winner.updated_at | date }}</td>
                                    </tr>
                                    <tr>
                                        <th>Raffle Number :</th>
                                        <td>{{ winner.raffle_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name :</th>
                                        <td>{{ winner.fullname }}</td>
                                    </tr>
                                    <tr>
                                        <th>CPR :</th>
                                        <td>{{ winner.cpr }}</td>
                                    </tr>
                                    <tr>
                                        <th>Mobile :</th>
                                        <td>{{ winner.mobile }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email :</th>
                                        <td>{{ winner.email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nationality :</th>
                                        <td v-if="winner.nationality">{{ winner.nationality.name }}</td>
                                        <td v-else></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- <div class="widget-user-card__info">
                            <div class="widget-user-card__name">{{ winner.fullname }}</div>
                            <div class="widget-user-card__occupation"><b>Mobile :</b> {{ winner.mobile }}</div>
                            <div class="widget-user-card__occupation"><b>Email :</b> {{ winner.email }}</div>
                            <div class="widget-user-card__occupation"><b>CPR :</b> {{ winner.cpr }}</div>
                        </div> -->
                        <div class="no-print" style="margin-top: 30px;">      
                            <a href="javascipt:void(0)" class="btn btn-success btn-rounded btn-lg widget-user-card__follow" @click="prints(winner.winner_id)">Print</a>
                            <!-- <a href="#" class="btn btn-success btn-rounded btn-lg widget-user-card__follow" @select="winner">Select</a> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

</template>
<style>
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
}
.winner_selected th{
    text-align: right;
}
.winner_selected td{
    text-align: left;
}
</style>
<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                strings: "0000000",
                points: [],
                isRunning: false,
                timer: null,
                current_point: {},
                winner_selected: false,
                winner:{fullname : "sdasd"},
                winnerselected: false,
                lucky_draw: {},
            }
        },
        mounted: function () {
            this.getPoints();
            // this.selectWinners();
        },
        methods: {
            getPoints: function () {
                axios.get('/lucky-draw/' + this.$route.params.lucky_draw_id + '/points')
                    .then(r => r.data)
                    .then((response) => {
                        this.points = response.data;
                        this.winner_selected = false;
                        this.winner = {};
                        //this.start();
                    }).catch((error) => {
                        //notification('Select Winners', error.response.data.message, 'danger');
                    });
            },
            selectWinners: function () {
                axios.get('/lucky-draw/' + this.$route.params.lucky_draw_id + '/select/winners')
                    .then(r => r.data)
                    .then((response) => {
                        this.ShowWinners(this.$route.params.lucky_draw_id);
                    }).catch((error) => {
                        notification('Select Winners', error.response.data.message, 'danger');
                    });
            },
            start: function () {
                var counter = this.points.length,
                    index;
                var points = this.points;
                this.isRunning = true;
                //if (!this.timer) {
                    this.timer = setInterval(() => {
                        if (counter > 0) {
                            index = Math.floor(Math.random() * counter);
                            counter--;
                            this.strings = points[index].uuid.toUpperCase();
                            this.current_point = points[index];
                            this.isRunning = false;
                            console.log(index);
                        } else {
                            clearInterval(this.timer);
                        }
                    }, 100)
                //}
            },
            selectWinner: function () {
                axios.get('/lucky-draw/' + this.$route.params.lucky_draw_id + '/point/'+ this.current_point.id +'/customer')
                    .then(r => r.data)
                    .then((response) => {
                        this.winner = response.data;
                        this.lucky_draw = response.lucky_draw;
                        this.winner_selected = true;
                        //$('#app > div').css('height', '100vh');
                    }).catch((error) => {
                        notification('Select Winners', error.response.data.message, 'danger');
                    });
            },
            stop: function () {
                this.isRunning = false;
                clearInterval(this.timer);
                this.timer = null;
                this.winnerselected = true;
                //setTimeout(function () { this.selectWinner() }.bind(this), 5000);
            },
            reset: function(){
                axios.get('/lucky-draw/' + this.$route.params.lucky_draw_id + '/reset')
                    .then(r => r.data)
                    .then((response) => {
                       this.getPoints();
                    }).catch((error) => {
                    });
            },
            next: function(){
                this.selectWinner();
            },
            print: function(){
                window.print();
            },
            prints(id){
                let routeData = this.$router.resolve({
                    path: '/winner-print/pdf/' + id,
                    query: {}
                });
                window.open(routeData.href, '_blank');
            },
            shuffleArray: function (array) {
                var counter = array.length,
                    temp, index;
                while (counter > 0) {
                    index = Math.floor(Math.random() * counter);
                    counter--;
                    temp = array[counter];
                    array[counter] = array[index];
                    array[index] = temp;
                }
                return array;
            },
            ShowWinners: function (id) {
                this.$router.push({
                    path: '/lucky-draws/' + id + '/winners'
                });
            },
        }
    }

</script>

<style scoped> scoped>
    .ld-main-container {
        width: 100%;
        /* margin-bottom: 20px;
        padding: 20px; */
        background-color: #fff;
        border-radius: 3px;
        -webkit-box-shadow: 0 2px 5px 0 rgba(147, 157, 170, 0.03);
        box-shadow: 0 2px 5px 0 rgba(147, 157, 170, 0.03);
        font-size: 15px;
        padding: 20px;
    }

    .ld-container {
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
        align-items: center;
    }

    .ld-footer {
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
        padding-top: 30px;
        align-items: center;
    }

    .ld-container>div {
        background-color: #8c067a;
        color: #ffffff;
        width: 60px;      
        margin: 4px;  
        text-align: center;
        line-height: 50px;
        font-size: 30px;
    }

    /* @media only screen and (max-width: 600px) {
         .ld-container>div {
             margin: 10px;
         }
    } */

    @media screen and (max-width: 600px) {
        .ld-main-container {
            padding: 10px;
            margin-top: 50%;
        }
        .ld-container>div {
            margin: 4px;
            line-height: 40px;
            font-size: 25px;
        }
    }

    @media screen and (min-width: 1200px) {
        .ld-container>div {
            margin: 10px;
        }
    }

    .card-top {
        height: 350px;
        background-size: cover;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
    }

</style>
