<template>
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h1>Calendar</h1>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <form @submit.prevent="addEvent">
                  <div class="col-md-12 my-3">
                    <label>Event</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="event.title"
                      :class="{ 'is-invalid': error.hasOwnProperty('title') }"
                    />
                    <label
                      class="text-danger"
                      v-if="error.hasOwnProperty('title')"
                      >{{ error.title[0] }}</label
                    >
                  </div>
                  <div class="col-md-12 my-3">
                    <div class="row">
                      <div class="col-md-6">
                        <label>From</label>
                        <input
                          type="date"
                          class="form-control"
                          v-model="event.from"
                          :class="{
                            'is-invalid': error.hasOwnProperty('from'),
                          }"
                        />
                        <label
                          class="text-danger"
                          v-if="error.hasOwnProperty('from')"
                          >{{ error.from[0] }}</label
                        >
                      </div>
                      <div class="col-md-6">
                        <label>To</label>
                        <input
                          type="date"
                          class="form-control"
                          v-model="event.to"
                          :class="{
                            'is-invalid': error.hasOwnProperty('to'),
                          }"
                        />
                        <label
                          class="text-danger"
                          v-if="error.hasOwnProperty('to')"
                          >{{ error.to[0] }}</label
                        >
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 my-3">
                    <div class="d-flex">
                      <div
                        class="mx-2"
                        v-for="(day, index) in days"
                        :key="index"
                      >
                        <input
                          type="checkbox"
                          :value="day"
                          v-model="event.days"
                          :class="{
                            'is-invalid': error.hasOwnProperty('days'),
                          }"
                        />
                        <label :for="day">{{ day }}</label>
                      </div>
                    </div>
                    <label
                      class="text-danger"
                      v-if="error.hasOwnProperty('days')"
                      >{{ error.days[0] }}</label
                    >
                  </div>
                  <div class="col-md-12 my-3">
                    <button class="btn btn-primary">Save</button>
                  </div>
                </form>
              </div>
              <div class="col-md-8">
                <h1>July 2021</h1>
                <hr />
                <div class="row">
                  <div class="col-md-12">
                    <table class="table">
                      <tbody>
                        <tr
                          v-for="(day, index) in daysWithName"
                          :key="index"
                          :class="{ 'table-success': day.available }"
                        >
                          <td>{{ day.day }}</td>
                          <td>{{ day.event }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      days: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
      days_in_month: 0,
      event: {
        days: [],
      },
      copy_event: {
        days: [],
      },
      error: {},
    };
  },
  mounted() {
    console.log("Component mounted.");
    this.days_in_month = new Date(2021, 7, 0).getDate();
    axios.get(`api/events`).then((response) => {
      this.event = response.data;
      this.copy_event = JSON.parse(JSON.stringify(this.event));
    });
  },
  methods: {
    addEvent() {
      axios
        .post(`api/events`, this.event)
        .then((response) => {
          axios.get(`api/events`).then((response) => {
            this.event = response.data;
            this.copy_event = JSON.parse(JSON.stringify(this.event));
          });
        })
        .catch((error) => {
          if (error.response.status === 422) {
            this.error = error.response.data.errors;
          }
        });
    },
  },
  computed: {
    daysWithName() {
      var days = [];

      var title = "";
      var event_from = 0;
      var event_to = 0;

      if (this.copy_event.hasOwnProperty("id")) {
        title = this.copy_event.title;
        event_from = new Date(this.copy_event.from).getDate();
        event_to = new Date(this.copy_event.to).getDate();
      }

      var index = 1;
      while (index <= this.days_in_month) {
        var date = new Date(2021, 6, index);
        var day = this.days[date.getDay(index)];

        var available = false;
        available =
          this.copy_event.hasOwnProperty("id") &&
          this.copy_event.days.some((element) => element === day)
            ? true
            : false;

        if (
          date.getDate() >= event_from &&
          date.getDate() <= event_to &&
          available
        ) {
          days.push({
            day: `${index} ${day}`,
            event: title,
            available: true,
          });
        } else {
          days.push({
            day: `${index} ${day}`,
            event: "",
            available: false,
          });
        }

        index++;
      }
      return days;
    },
  },
};
</script>
