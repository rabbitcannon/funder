import "babel-polyfill";
import React, { Component } from "react";
import ReactDOM from "react-dom";
import Axios from "axios";

import Index from "./layout/Index";
import LoginForm from "./login/LoginForm";

class EntryPoint extends Component {
    constructor(props) {
        super(props);

        this.state ={
        	loggedIn: false,
			errorMessage: '',
			player: sessionStorage.getItem('playerData') || {},
        }
    }

    componentDidMount = () => {
    	if(sessionStorage.playerData != null) {
			this.setState({
				loggedIn: true,
			});
		}
	}

	handleSubmit = async (event) => {
		event.preventDefault();
		$('#login-btn').html('<img src="../../images/loaders/loader_pink_15_sharp.svg" /> Logging In');
		$('#reset-btn').hide();

		let $error = $('span.error-msg');

		this.setState({errorMessage: ''});

		$error.removeClass('animated fadeIn');

		await Axios.post('/api/funding/login', {
			email: $('[name="email"]').val(),
			password: $('[name="password"]').val(),
			registrar_id: $('[name="registrar_id"]').val()
		}).then((response) => {
			let results = JSON.stringify(response.data);
			sessionStorage.setItem('playerData', results);
			this.setState({
				loggedIn: true,
				player: results
			});
		}).catch((error) => {
			console.log(error);
			this.setState({
				errorMessage: "Error logging in, please try again."
			});
			$('#login-btn').html('Login');
			$('#reset-btn').show();
			$error.addClass('animated fadeIn');
		});
	}

    renderLoginPanel = () => {
        return (
            <div>
                <LoginForm auth={this.state.loggedIn} handleSubmit={this.handleSubmit.bind(this)}
						   errorMessage={this.state.errorMessage} />
            </div>
        );
    }

    renderProfilePanel = () => {
		return (
			<div>
				<Index playerData={this.state.player} />
			</div>
		);
    }

    render() {
		if(this.state.loggedIn === true) {
		    return this.renderProfilePanel()
		}
		else {
		    return this.renderLoginPanel();
        }
    }
}

if (document.getElementById('app')) {
    ReactDOM.render(<EntryPoint />, document.getElementById('app'));
}
