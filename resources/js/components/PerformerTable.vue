<template>
    <div>
        <group-table v-if="parsed"
                     :fields="['taskname', 'labor', 'nonlabor', 'total']"
                     :headers="['Task Name','Labor','Non Labor','Total']"
                     group-by="name"
                     :totals="['labor', 'nonlabor', 'total']"
                     :items="getItems"
                     :currency="['labor','nonlabor','total']"
                     group-header-column="taskname"
        ></group-table>
        <div v-if="error" class="font-bold">
            There was an error loading data. Please check back later.
        </div>
    </div>
</template>

<script>
import GroupTable from "./GroupTable";

class Performer {
    constructor(id, display_name) {
        this.id = id;
        this.display_name = display_name;
        this.tasks = {};
    }

    addTask(task) {
        this.tasks[task.id] = task;
    }
}

class Task {

    constructor(id, title) {
        this.id = id;
        this.title = title;
        this.laborItems = {};
        this.nonLaborItems = [];
    }

    totalLabor() {
        let total = 0;
        Object.keys(this.laborItems).forEach(id => {
            Object.keys(this.laborItems[id].fiscal_years).forEach((year) => {
                total += this.laborItems[id].fiscal_years[year].total_dollars;
            });
        });
        return total;
    }

    totalNonLabor() {
        let total = 0;
        this.nonLaborItems.forEach((item) => {
            Object.keys(item.fiscal_years).forEach((year) => {
                total += item.fiscal_years[year].total_dollars;
            });
        });
        return total;
    }

    total() {
        return this.totalLabor() + this.totalNonLabor()
    }
}

class LaborItem {
    constructor(type, id, title, code, fiscal_years) {
        this.type = type;
        this.id = id;
        this.title = title;
        this.code = code;
        this.fiscal_years = fiscal_years;
    }
}

class NonLaborItem {
    constructor(type, title, fiscal_years) {
        this.type = type;
        this.title = title;
        this.fiscal_years = fiscal_years;
    }
}


export default {
    name: "PerformerTable",
    components: {GroupTable: GroupTable},
    data() {
        return {
            data: null,
            parsed: false,
            error: false,
            perfs: {}
        }
    },
    mounted() {
        axios.get('/api/performers').then(response => {
            this.data = response.data;
            this.parsed = this.parseInput(this.data);
        }).catch(error => {
            this.error = true;
        });
    },
    computed: {
        getItems() {
            let items = [];
            Object.keys(this.perfs).forEach((id) => {
                let name = this.perfs[id].display_name;
                Object.keys(this.perfs[id].tasks).forEach((task_id) => {
                    const laborTotal = this.sumTotalDollars(this.perfs[id].tasks[task_id].laborItems);
                    const nonlaborTotal = this.sumTotalDollars(this.perfs[id].tasks[task_id].nonLaborItems);
                    const total = laborTotal + nonlaborTotal;
                    let item = {
                        name: name,
                        taskname: this.perfs[id].tasks[task_id].title,
                        labor: laborTotal,
                        nonlabor: nonlaborTotal,
                        total: total
                    };
                    items.push(item);
                })
            });
            return items;
        },

    },
    methods: {
        sumTotalDollars(list) {
            // return 0;
            let sum = 0;
            let values = [];
            if (Array.isArray(list)) {
                values = list;
            } else {
                values = Object.values(list);
            }
            values.forEach(obj => {
                Object.values(obj.fiscal_years).forEach(fy => {
                    sum += fy.total_dollars;
                })
            });
            return sum;
        },

        parseInput(data) {
            // console.group('parsing data', data, data.cost_data.data.labor.groups);
            data.cost_data.data.labor.groups.forEach((group) => {
                let performer = group.performer;
                if (!this.perfs.hasOwnProperty(performer.id)) {

                    let perf = new Performer(performer.id, performer.display_name);
                    this.perfs[performer.id] = perf;
                } else {
                    console.group('already loaded', performer.id, this.perfs[performer.id], this.perfs)
                }
                group.tasks.forEach(task => {
                    if (!this.perfs[performer.id].tasks.hasOwnProperty(task.id)) {
                        this.perfs[performer.id].tasks[task.id] = new Task(task.id, task.title);
                    }
                    task.items.forEach(item => {
                        if (!this.perfs[performer.id].tasks[task.id].laborItems.hasOwnProperty(item.id)) {
                            this.perfs[performer.id].tasks[task.id].laborItems[item.id] = new LaborItem('labor', item.id, item.title, item.code, item.fiscal_years);
                        }
                    });
                });
            });
            data.cost_data.data.non_labor.groups.forEach((group) => {
                let performer = group.performer;
                if (!this.perfs.hasOwnProperty(performer.id)) {
                    let perf = new Performer(performer.id, performer.display_name);
                    this.perfs[performer.id] = perf;
                }
                group.tasks.forEach(task => {
                    if (!this.perfs[performer.id].tasks.hasOwnProperty(task.id)) {
                        this.perfs[performer.id].tasks[task.id] = new Task(task.id, task.title);
                    }
                    task.items.forEach(item => {
                        this.perfs[performer.id].tasks[task.id].nonLaborItems.push(new NonLaborItem('non-labor', item.title, item.fiscal_years));
                    });
                });
            });
            return true;
        }
    }
}
</script>

<style scoped>


</style>
