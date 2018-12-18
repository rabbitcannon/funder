import React, {Component} from "react";
import Axios from "axios";
import Toastr from "toastr";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {faCheck, faTimes} from "@fortawesome/free-solid-svg-icons";
import Foundation from "foundation-sites";

import Address from '../layout/controls/Address';
import CreditCard from "../layout/controls/CreditCard";
import config from  "../../config/config.json";

const API_KEY = btoa(config.keys.paysafe);
const data = JSON.parse(sessionStorage.getItem('playerData'));
let instance = null;

Toastr.options.closeMethod = 'fadeOut';
Toastr.options.closeDuration = 300;
Toastr.options.closeEasing = 'swing';
Toastr.options.closeButton = true;
Toastr.options.preventDuplicates = true;
Toastr.options.progressBar = true;

let OPTIONS = {
	environment: "TEST",
	fields: {
		cardNumber: {
			fieldName: "#card-number",
			placeholder: "card number",
			selector: "#card-number"
		},
		expiryDate: {
			fieldName: "#exp-date",
			placeholder: "MM/YY",
			selector: "#exp-date",
		},
		cvv: {
			fieldName: "#cvv",
			placeholder: "cvv",
			selector: "#cvv",
		}
	}
};


class AddCreditCard extends Component {
	constructor(props) {
		super(props);

		this.state = { nicknameValid: true }
	}

	componentDidMount = async () => {
		$(document).foundation();

		await window.paysafe.fields.setup(API_KEY, OPTIONS, function(paysafeInstance, error) {
			if(error) {
				console.log("Setup error: " + error.code + " " + error.detailedMessage);
			}
			else {
				instance = paysafeInstance;
			}
		});

		this.checkNickname();
	}

	checkNickname = () => {
		let nickname = $('#account-nickname');
		let validCheck = null;

		nickname.on('blur', function() {
			if(nickname.val() != "") {
				$('#nickname-loader').show();
				$('#nickname-success').hide();
				$('#nickname-failed').hide();

				Axios.post('/api/nickname/check', {
					playerHash: data.player.playerhash,
					nickname: $(this).val().trim(),
					type: "card_profiles"
				}).then((response) => {
					if(response.data.valid === true) {
						$('#nickname-loader').hide();
						$('#nickname-success').fadeIn('fast');
						validCheck = true;
					}
					else {
						$('#nickname-loader').hide();
						$('#nickname-failed').fadeIn('fast');
						validCheck = false;
					}
					this.setState({
						nicknameValid: response.data.valid
					}, console.log(this.state.nicknameValid));
				}).catch((error) => {
					console.log(error);
				});

			}
		});
	}

	handlePayment = (event) => {
		event.preventDefault();

		$('form#add-card-form').foundation('validateForm');
		$('#add-card-btn').html('<img src="../../images/loaders/loader_pink_15_sharp.svg" /> Saving');

		if(!instance) {
			console.log("No instance");
		}

		if(this.state.validNickname === true) {
			instance.tokenize((paysafeInstance, error, result) => {
				if(error) {
					console.log("Tokenization error: " + error.code + " " + error.detailedMessage);
					$('#add-card-btn').html('Add Card');
					Toastr.error(error.detailedMessage);
				}
				else {
					let defaultCheck = $('#make_default').is(':checked')
					let checkValue = null;

					if(defaultCheck) {
						checkValue = true;
					}
					else {
						checkValue = false;
					}

					Axios.post('/api/methods/add', {
						playerHash: data.player.playerhash,
						provider_temporary_token: result.token,
						payment_method_nickname: $('#account-nickname').val(),
						funding_method_type: "card_profile",
						default: checkValue,
						billing_details: {
							address_nickname: null,
							address1: $('#address_1').val(),
							address2: $('#address_2').val(),
							city: $('#city').val(),
							state: $('#state').val(),
							country: 'US',
							zip: $('#zip').val(),
						}
					}).then((response) => {
						this.updatePaymentMethods();
						Toastr.success('Payment method saved.');
						$('form#add-card-form').trigger("reset");
						$('#card-number').text("");
						$('#exp-date').text("");
						$('#cvv').text("");
						$('#add-card-btn').html('Add Card');
					}).catch((error) => {
						Toastr.error('Error saving payment method.');
						$('#add-card-btn').html('Add Card');
						console.log(error);
					});
				}
			});
		}
	}

	updatePaymentMethods = async () => {
		await this.props.updatePaymentMethods();
	}

    render() {
		const styles = {
			hidden: {
				display: 'none'
			}
		}

        return (
			<div className="card animated fadeIn">
				<div className="card-divider">
					<h4>Add a new credit card</h4>
				</div>
				<div className="card-section">

					<form id="add-card-form" data-abide noValidate>
						<div className="grid-container">

							<div className="grid-x grid-margin-x">
								<div className="cell medium-4">
									<label htmlFor="account-nickname">Account Nickname&nbsp;
										<span id="nickname-loader" style={styles.hidden}><img src="../../images/loaders/loader_black_15.gif" /></span>
										<span id="nickname-failed" style={styles.hidden} className="error"><FontAwesomeIcon icon={faTimes} /> Name already in use.</span>
										<span id="nickname-success" style={styles.hidden} className="success"><FontAwesomeIcon icon={faCheck} /> Name available!</span>

										<input id="account-nickname" type="text" placeholder="account nickname"
											   aria-errormessage="numberError" required />
									</label>
									<span className="form-error" id="nickname-error" data-form-error-for="account-nickname">
										Please add a name to identify this account.
									</span>
								</div>
							</div>

							<div className="grid-x grid-margin-x">
								<Address />
								<CreditCard showDefault={true}/>
							</div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12 text-center">
									<span className="form-error" id="form-card-error"></span>
								</div>
							</div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12 text-center">
									<button id="add-card-btn" className="button" onClick={this.handlePayment}>Add Card</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
        );
    }
}

export default AddCreditCard;