import React, {Component} from "react";
import Axios from "axios";
import Toastr from "toastr";
import Foundation from "foundation-sites";

import Address from '../layout/controls/Address';
import CreditCard from "../layout/controls/CreditCard";

import config from  "../../config/config.json";
import FundAmount from "../layout/controls/FundAmount";
import FundingBlock from "./FundingBlock";

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
		console.log(this.props.balance);
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
		let $errorSpan = $("#form-submit-error");
		$errorSpan.text("");
		$('#add-funds-btn').html('<img src="../../images/loaders/loader_pink_15.svg" /> Adding funds');

		event.preventDefault();

		if(!instance) {
			console.log("No instance");
		}

		instance.tokenize((paysafeInstance, error, result) => {
			if(error) {
				$errorSpan.text("Tokenization error: " + error.code + " " + error.detailedMessage)
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
					let message = "Funding successful";
					if(defaultCheck) {
						message += " and payment method saved";
					}
					Toastr.success(message + "!");
					$('form#add-funds-form').trigger("reset");
					$('#add-funds-btn').html('Add Funds');
					this.updateBalance();
					this.setState({
						additionalAmount: 0,
						newAmount: 0,
					});
				}).catch((error) => {
					$('#add-funds-btn').html('Add Funds');
					Toastr.error('Error: Unable to add funds.');
					console.log(error);
				});
			}
		});
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
                                <div className="cell medium-12">
                                    <div data-abide-error className="alert callout" style={styles.hidden}>
                                        <p><i className="fi-alert"></i> There are some errors in your form.</p>
                                    </div>
                                </div>
                            </div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-6">
                                    <Address/>
								</div>
								<div className="cell medium-6">
									<CreditCard/>
									<FundAmount handleAmountChange={this.handleAmountChange}/>

									<div className="grid-x grid-margin-x">
										<div className="cell medium-12">
											<input id="save_payment" name="save_payment" type="checkbox" onChange={this.handleVisibility} />
											<label htmlFor="save_payment">Save payment method?</label>
										</div>
									</div>

									<div className="grid-x grid-margin-x" style={{ display: this.state.saveVisible == true ? 'block': 'none'}}>
										<div className="cell medium-4">
											<label htmlFor="account-nickname">Account Nickname
												<input id="account-nickname" type="text" placeholder="account nickname"
													   aria-errormessage="numberError" required />
											</label>
											<span className="form-error" id="nickname-error" data-form-error-for="account-nickname">
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