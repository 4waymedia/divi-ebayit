// External Dependencies
import React, { Component, Fragment } from 'react';

// Internal Dependencies
import './style.css';

class EbayProjector extends Component {

  static slug = 'wpep_ebay_projector';

  render() {
    // Visual Builder Display for DIVI Module content
    return (
      <Fragment> 
        <h1 className="wpep-header">{this.props.heading}</h1>
        <table>
          <tr>
            <td>{this.props.orientation} {this.props.template_aspect}</td>
          </tr>
          <tr>
            <td>
              <ul>
                <li>Store ID: {this.props.seller}</li>
                <li>Keyword: {this.props.keywords}</li>
                <li>Display: {this.props.template}</li>
                <li>Columns: {this.props.columns}</li>
                <li>Limit: {this.props.limit}</li>
                <li>Search: {this.props.search}</li>
                <li>Paginate: {this.props.paginate}</li>
                <li>Image size: {this.props.imagesize}</li>
              </ul>
            </td>
            <td>
              
            </td>
          </tr>
        </table>
      </Fragment>
    );
  }
}

export default EbayProjector;
