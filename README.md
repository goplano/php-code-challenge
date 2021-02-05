#Code Challenge

See "[CodeChallenge.pdf](/CodeChallenge.pdf)" found in root folder for instructions

data is located in
[/data.json](/data.json)

Create a fork off of this repository
https://gist.github.com/Chaser324/ce0505fbed06b947d962

Then Create a pull request merging your fork into ours
https://docs.github.com/en/github/collaborating-with-issues-and-pull-requests/creating-a-pull-request-from-a-fork


Please create a pull request and let us know when you are complete

## Notes

There are two components, PerformerTable and GroupTable.

PerformerTable fetches the data and prepares it for the GroupTable.

### PerformerTable
The code gets the job done, but I could spend more time on this. I would consider having the controller do the heavy lifting, though, rather than have the component do it. 

### GroupTable
This component expects a simple array of objects.  Grouping and totals are optional.

Sample row

```javascript
{
    labor: 105000
    name: "Applied Physics Lab (APL) (APL001 CLIN 00001)"
    nonlabor: 0
    taskname: "Capabilities Based Assessment3 Cow"
    total: 105000
}
```

```javascript
    <group-table v-if="parsed"
                 :fields="['taskname', 'labor', 'nonlabor', 'total']"
                 :headers="['Task Name','Labor','Non Labor','Total']"
                 group-by="name"
                 :totals="['labor', 'nonlabor', 'total']"
                 :items="getItems"
                 :currency="['labor','nonlabor','total']"
                 group-header-column="taskname"
    ></group-table>
```

- ```fields``` is a list of the data in the order they appear
- ```items``` is an array of simple objects containing the table data.  see example above
- ```headers``` (optional) is list matching column headers
- ```group-by``` (optional) is an optional field to use for groups 
- ```totals``` (optional) is a list of fields to sum for a group
- ```currency``` (optional) is a list of fields that should display as currency
- ```group-header-column``` (optional) if set the value of the group-by field will be displayed in this column in the group header

The table comes with default styling.  These styles can be overridden, but the specificity must more specific.
This can be achieved by adding ```table.group-table``` or ```td.group-table-cell``` .  All the classes pertain to a table cell except for ```.group-table```.


Default styling
```css
<style>
.group-table {
    width: 100%;
    table-layout: auto;
    border: 1px solid black;
    border-collapse: collapse;
}

.group-table-cell {
    border: 1px solid black;
    padding: .25rem .75rem;
    height: 1.2rem;
}

.group-table-header-cell {
    font-weight: bold;
    white-space: nowrap;
}

.group-table-even {
    color: rgb(31, 41, 55);
    background-color: white;
}

.group-table-odd {
    color: rgb(31, 41, 55);
    background-color: rgb(239, 246, 255);
}

.group-table-currency {
    text-align: right;
}
</style>
```
