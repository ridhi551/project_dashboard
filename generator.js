function TCMap(teacherId) {
    this.teacherId = teacherId;
    this.flag = -1;
    this.count = 0;
}

TCMap.prototype.updateFlag = function(flag) {
    this.flag = flag;
};

TCMap.prototype.updateCount = function() {
    this.count++;
};

function TimeTable() {
    this.fourthSem = []
    this.sixthSem = [];

    for (let i = 0; i < 5; i++) {
        this.fourthSem.push(Array(4).fill(0));
        this.sixthSem.push(Array(4).fill(0));
    }
}

TimeTable.prototype.copyDay = function(day, dayLabel) {
    for (let i = 0; i < 4; i++) {
        this.fourthSem[dayLabel][i] = day[0][i];
    }
    for (let i = 0; i < 4; i++) {
        this.sixthSem[dayLabel][i] = day[1][i];
    }
};

TimeTable.prototype.printTable = function(teacherMap) {
    console.log("Time Table of 4th Semester");
    for (let i = 0; i < this.fourthSem.length; i++) {
        let row = "";
        for (let j = 0; j < this.fourthSem[i].length; j++) {
            const teacherId = this.fourthSem[i][j];
            const teacherName = teacherMap.get(teacherId);
            row += teacherName ? teacherName.padEnd(15) : "Unknown".padEnd(15);
        }
        console.log(row);
    }
    console.log("\n");
    console.log("Time Table of 6th Semester");
    for (let i = 0; i < this.sixthSem.length; i++) {
        let row = "";
        for (let j = 0; j < this.sixthSem[i].length; j++) {
            const teacherId = this.sixthSem[i][j];
            const teacherName = teacherMap.get(teacherId);
            row += teacherName ? teacherName.padEnd(15) : "Unknown".padEnd(15);
        }
        console.log(row);
    }
};



TimeTable.prototype.create = function(s4, s6, teacherMap) {
    let dayLabel, x;
    const min = 0;
    const max = 4;

    for (x = 0, dayLabel = 0; x <= 4; x++, dayLabel++) {
        const randomNumber = Math.floor(Math.random() * (max - min) + min);
        this.copyDay(permutations(randomNumber, randomNumber, s4, s6), dayLabel);
    }
    this.printTable(teacherMap);
};

function printMap(demo) {
    for (let i = 0; i < demo.length; i++) {
        console.log(demo[i].teacherId + " <> " + demo[i].flag);
    }
    console.log();
}

function search(list, element) {
    for (let i = 0; i < list.length; i++) {
        if (element === list[i].teacherId) {
            return list[i].flag;
        }
    }
    return -999;
}

function permutations(i, j, s4, s6) {
    const day = [[], []];
    const classes = 4;
    let ptr = 0;

    for (let period of day) {
        period.push(...Array(4).fill(0));
    }

    for (; ptr < classes; i = (i + 1) % s4.length, ptr++) {
        day[0][ptr] = s4[i].teacherId;
        s4[i].flag = ptr;
    }
    ptr = 0;

    for (; ptr < classes; j = (j + 1) % s6.length, ptr++) {
    
        day[1][ptr] = s6[j].teacherId;
        s6[j].flag = ptr;
        if (search(s4, s6[j].teacherId) === s6[j].flag) {
            s6[j].flag = -1;
            --ptr;
        }
        if (ptr === 3) {
            break;
        }
    }
    return day;
}

const teacherMap = new Map();
teacherMap.set(101, "Jyoti Sir");
teacherMap.set(102, "Bhawna Mam");
teacherMap.set(103, "Sheetal Mam");
teacherMap.set(107, "Akhil Sir");
teacherMap.set(109, "Heera Sir");
teacherMap.set(110, "Neeraj Sir");
teacherMap.set(111, "Simmi Mam");

const s4 = [new TCMap(101), new TCMap(102), new TCMap(103), new TCMap(107), new TCMap(109), new TCMap(110)];
const s6 = [new TCMap(111), new TCMap(107), new TCMap(103), new TCMap(102), new TCMap(101)];

const object = new TimeTable();
object.create(s4, s6, teacherMap);
