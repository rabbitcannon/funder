import React, {Component} from "react";
import Axios from "axios";
import Toastr from "toastr";
import Foundation from "foundation-sites";

import Address from '../layout/controls/Address';
import CreditCard from "../layout/controls/CreditCard";
import FundAmount from "../layout/controls/FundAmount";
import FundingBlock from "./FundingBlock";

import config from  "../../config/config.json";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faCheck, faTimes} from "@fortawesome/free-solid-svg-icons";

const API_KEY = btoa(config.keys.paysafe);
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

class OneTimeFunding extends Component {
	constructor(props) {
		super(props);

		this.state = {
			additionalAmount: 0,
			balance: this.props.balance,
			newAmount: 0,
			playerData: sessionStorage.getItem('playerData'),
			saveVisible: false
		}

		this.updateBalance = this.updateBalance.bind(this);
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

	checkNickname = async () => {
		let nickname = $('#onetime-account-nickname');
		let data = JSON.parse(sessionStorage.getItem('playerData'));

		await nickname.on('blur', function() {
			if(nickname.val() != "") {
				$('#onetime-nickname-loader').show();
				$('#onetime-nickname-success').hide();
				$('#onetime-nickname-failed').hide();
				Axios.post('/api/nickname/check', {
					playerHash: data.player.playerhash,
					nickname: $(this).val().trim(),
					type: "card_profiles"
				}).then((response) => {
					if(response.data.valid === true) {
						$('#onetime-nickname-loader').hide();
						$('#onetime-nickname-success').fadeIn('fast');
					}
					else {
						$('#onetime-nickname-loader').hide();
						$('#onetime-nickname-failed').fadeIn('fast');
					}
				}).catch((error) => {
					console.log(error);
				});
			}
		});
	}

	handleAmountChange = (event) => {
		let newFundsPence = event.target.value * 100;
		let currency = parseInt(event.target.value).toFixed(2);
		let newBalance = newFundsPence + this.props.balance;

		this.setState({
			additionalAmount: currency, newAmount: newBalance
		});
	}

	handlePayment = (event) => {
		event.preventDefault();

		$('form#add-funds-form').foundation('validateForm');
		$('#add-funds-btn').html('<img src="../../images/loaders/loader_pink_15_sharp.svg" /> Adding funds');

		if(!instance) {
			console.log("No instance");
		}

		instance.tokenize((paysafeInstance, error, result) => {
			if(error) {
				Toastr.error("Tokenization error: " + error.code + " " + error.detailedMessage);
				$('#add-funds-btn').html('Add Funds');
				Toastr.error(error.detailedMessage);
			}
			else {
				let data = JSON.parse(sessionStorage.getItem('playerData'));
				let amount = parseInt($('#fund-amount').val()) * 100;
				let defaultCheck = $('#save_payment').is(':checked');
				let saveValue = null;

				if(defaultCheck) {
					saveValue = true;
				}
				else {
					saveValue = false;
				}

				Axios.post('/api/funds/add', {
					playerHash: data.player.playerhash,
					amount: amount,
					provider_temporary_token: result.token,
					payment_method_nickname: $('#account-nickname').val(),
					funding_method_type: "card_profile",
					save_method: saveValue,
					default: false,
					billing_details: {
						address_nickname: null,
						address1: $('#address_1').val(),
						address2: $('#address_2').val(),
						city: $('#city').val(),
						state: $('#state').val(),
						country: 'US',
						zip: $('#zip').val(),
					}
				}).then(() => {
					$('form#add-funds-form').trigger("reset");
					$('#add-funds-btn').html('Add Funds');

					let message = "Funding successful";

					if(defaultCheck) {
						message += " and payment method saved";
					}

					this.setState({
						additionalAmount: 0,
						newAmount: 0,
					});
					this.updateBalance();

					Toastr.success(message + "!");
				}).catch((error) => {
					$('#add-funds-btn').html('Add Funds');
					Toastr.error('Error: Unable to add funds.');
					console.log(error);
				});
			}
		});
	}

	updatePaymentMethods = async () => {
		await this.props.updatePaymentMethods();
	}

	handleVisibility = () => {
		if($('#save_payment').is(':checked')) {
			this.setState({ saveVisible: true })
		}
		else {
			this.setState({ saveVisible: false })
		}
	}

	updateBalance = () => {
		this.props.updateBalance();
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
                    <h4>Add funds</h4>
                </div>

                <div className="card-section">

					<div>
						<FundingBlock balance={this.props.balance} additionalAmount={this.state.additionalAmount} newAmount={this.state.newAmount}/>
					</div>

                    <form id="add-funds-form" data-abide noValidate>
                        <div className="grid-container">

							<div className="grid-x grid-margin-x">
								<div className="cell medium-6">
                                    <Address/>
								</div>
								<div className="cell medium-6">
									<CreditCard/>
									<FundAmount handleAmountChange={this.handleAmountChange}/>

									<div className="grid-x grid-margin-x">
										<div className="cell medium-5">
											<input id="save_payment" name="save_payment" type="checkbox" onChange={this.handleVisibility} />
											<label htmlFor="save_payment">Save payment method?</label>
										</div>

										<div className="cell medium-7" style={{ display: this.state.saveVisible == true ? 'block': 'none'}}>
											<label htmlFor="onetime-account-nickname">Account Nickname&nbsp;
												<span id="onetime-nickname-loader" style={styles.hidden}><img src="../../images/loaders/loader_black_15.gif" /></span>
												<span id="onetime-nickname-failed" style={styles.hidden} className="error"><FontAwesomeIcon icon={faTimes} /> Name already in use.</span>
												<span id="onetime-nickname-success" style={styles.hidden} className="success"><FontAwesomeIcon icon={faCheck} /> Name available!</span>

												<input id="onetime-account-nickname" type="text" placeholder="account nickname"
													   aria-errormessage="numberError" required />
											</label>
											<span className="form-error" id="nickname-error" data-form-error-for="onetime-account-nickname">
												Please add a name to identify this account.
											</span>
										</div>
									</div>
								</div>
							</div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12 text-center">
									<button id="add-funds-btn" className="button"
											onClick={(event) => this.handlePayment(event)}>Add Funds</button>
								</div>
							</div>
                        </div>
                    </form>

                </div>
            </div>
        );
    }
}

export default OneTimeFunding;