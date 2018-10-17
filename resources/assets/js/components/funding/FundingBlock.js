import React, {Component} from "react";
import CountUp from "react-countup";

class FundingBlock extends Component {
	constructor(props) {
		super(props);

		this.state = {
			playerData: sessionStorage.getItem('playerData'),
		}
	}

    render() {
		let data = JSON.parse(this.state.playerData);
		let player = data.player;
		let currentBalance = parseFloat(player.cashbalancepence).toFixed(2);
		let newBalance = parseFloat(this.props.newAmount).toFixed(2)

		console.log(typeof parseFloat(this.props.newAmount).toFixed(2))

        return (
			<div className="grid-container">

				<div className="grid-x grid-margin-x text-center fund--table">
					<div className="cell medium-4 __item">
						<div>
							<span>Current Wallet</span>
						</div>
						<div>
							<span>${currentBalance}</span>
						</div>
					</div>
					<div className="cell medium-4 __item">
						<div>
							<span>Additional Funds</span>
						</div>
						<div>
							<span>
								<CountUp
									end={this.props.additionalAmount}
									decimals={2}
									prefix="$"
									duration={1.5} />
							</span>
						</div>
					</div>
					<div className="cell medium-4 __item">
						<div>
							<span>New Balance</span>
						</div>
						<div>
							{/*<span>${this.props.newAmount}</span>*/}
							<span>
								<CountUp
									end={newBalance}
									decimals={2}
									prefix="$"
									duration={1.5} />
							</span>
						</div>
					</div>
				</div>

			</div>
        );
    }
}

export default FundingBlock;