<template>
    <group-table v-if="parsed"
                 :fields="getFields"
                 group-by="name"
                 :totals="getTotals"
                 :items="getItems"
                 :formats="['labor','nonlabor','total']"
                 group-header-column="taskname"
    ></group-table>
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
            perfs: {}
        }
    },
    mounted() {
        axios.get('/api/performers').then(response => {
            this.data = response.data;
            this.parsed = this.parseInput(this.data);
        }).catch(error => console.log);
    },
    computed: {
        getItems() {
            let items = [];
            // console.log(Object.keys(this.perfs));
            Object.keys(this.perfs).forEach((id) => {
                let name = this.perfs[id].display_name;
                // console.log(name, id, this.perfs[id]);
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
        getFields() {
            return ['taskname', 'labor', 'nonlabor', 'total'];
        },
        getTotals() {
            return ['labor', 'nonlabor', 'total']
        },
        getTestItems() {
            return [
                {
                    category: "Yum",
                    name: 'Frozen Yogurt',
                    calories: 159,
                    fat: 6.0,
                    carbs: 24,
                    protein: 4.0,
                    iron: '1%',
                },
                {
                    category: "Yum",

                    name: 'Ice cream sandwich',
                    calories: 237,
                    fat: 9.0,
                    carbs: 37,
                    protein: 4.3,
                    iron: '1%',
                },
                {
                    category: "Yum",

                    name: 'Eclair',
                    calories: 262,
                    fat: 16.0,
                    carbs: 23,
                    protein: 6.0,
                    iron: '7%',
                },
                {
                    category: "Ick",

                    name: 'Cupcake',
                    calories: 305,
                    fat: 3.7,
                    carbs: 67,
                    protein: 4.3,
                    iron: '8%',
                },
                {
                    category: "Yum",

                    name: 'Gingerbread',
                    calories: 356,
                    fat: 16.0,
                    carbs: 49,
                    protein: 3.9,
                    iron: '16%',
                },
                {
                    category: "Yum",

                    name: 'Jelly bean',
                    calories: 375,
                    fat: 0.0,
                    carbs: 94,
                    protein: 0.0,
                    iron: '0%',
                },
                {
                    category: "Yum",

                    name: 'Lollipop',
                    calories: 392,
                    fat: 0.2,
                    carbs: 98,
                    protein: 0,
                    iron: '2%',
                },
                {
                    category: "Ick",

                    name: 'Honeycomb',
                    calories: 408,
                    fat: 3.2,
                    carbs: 87,
                    protein: 6.5,
                    iron: '45%',
                },
                {
                    category: "Yum",

                    name: 'Donut',
                    calories: 452,
                    fat: 25.0,
                    carbs: 51,
                    protein: 4.9,
                    iron: '22%',
                },
                {
                    category: "Ick",

                    name: 'KitKat',
                    calories: 518,
                    fat: 26.0,
                    carbs: 65,
                    protein: 7,
                    iron: '6%',
                },
            ];
        },
        getTestFields() {
            return ['name', 'calories', 'carbs'];
        },
        getTestTotals() {
            return ['carbs', 'calories'];
        }
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
                // console.log(this.perfs);
                if (!this.perfs.hasOwnProperty(performer.id)) {

                    let perf = new Performer(performer.id, performer.display_name);
                    this.perfs[performer.id] = perf;
                } else {
                    console.group('already loaded', performer.id, this.perfs[performer.id], this.perfs)
                }
                // console.log('tasks', group.tasks);
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
