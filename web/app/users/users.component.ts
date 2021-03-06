import {Component} from '@angular/core';
import {Router} from '@angular/router';

import {User} from './user';
import {UsersService} from './users.service';


@Component({
    templateUrl: 'app/users/users.component.html',
    providers: [UsersService]
})

export class UsersComponent {

    users:Array<User>;
    category:Array<any>;
    public currentPage:number = 1;
    public totalItems = 0;
    public maxSize:number = 5;

    constructor(private usersService:UsersService,
                private router:Router) {
        this.getUsers();

    }

    getUsers() {

        this.usersService.getUsers(this.currentPage)
            .subscribe(
                users => {
                    this.users = users.users;
                    this.totalItems = users.count;
                },
                error => console.log('onError: %s', error)
            );
    }

    getUser(id) {
        this.usersService.getUser(id)
            .subscribe(
                category => {
                    this.category = category;
                },
                error => console.log('onError: %s', error)
            );
    }

    editUser(user) {
        if (user) {
            this.router.navigate(['/backoffice/user/' + user.usr_id]);
        } else {
            this.router.navigate(['/backoffice/user/0']);
        }
    }

    deleteUser(user) {
        this.usersService.deleteUser(user.usr_id)
            .subscribe(
                result => this.getUsers(),
                error => alert('onError: %s' + error)
            );
    }

    public pageChanged():void {
        this.getUsers();
    };


}