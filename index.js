let cmd_select = document.getElementById('cmd');
let params = document.getElementById('params');
cmd_select.addEventListener('input', () => {
    let val = cmd_select.value;
    params.innerHTML = '';
    switch (val) {
        case "mlsd":
        case "chdir":
        case "delete":
        case "exec":
        case "get_option":
        case "mdtm":
        case "mkdir":
        case "nlist":
        case "raw":
        case "rmdir":
        case "site":
            // jeden argument
            let inp = document.createElement('input');
            inp.setAttribute('type', 'text');
            inp.setAttribute('name', 'arg1');
            params.appendChild(inp);
            break;
        case "append":
        case "chmod":
        case "get":
        case "put":
        case "fget":
        case "fput":
        case "nbput":
        case "nbget":
        case "rename":
        case "set_option":
            // dwa argumenty
            let inp1 = document.createElement('input');
            inp1.setAttribute('type', 'text');
            inp1.setAttribute('name', 'arg1');
            params.appendChild(inp1);
            let inp2 = document.createElement('input');
            inp2.setAttribute('type', 'text');
            inp2.setAttribute('name', 'arg2');
            params.appendChild(inp2);
            break;
        case "rawlist":
            // checkbox for recursive
            let dir = document.createElement('input');
            dir.setAttribute('type', 'text');
            dir.setAttribute('name', 'arg1');
            params.appendChild(dir);
            let c = document.createElement('input');
            c.setAttribute('type', 'checkbox');
            c.setAttribute('name', 'arg2');
            params.appendChild(c);
            let l = document.createElement('label');
            l.setAttribute('for', 'arg2');
            l.innerText = "(Use recursive?)"
            params.appendChild(l);
            break;
        case "cdup":
        case "pwd":
        case "systype":
        default:
            break;
    }
})