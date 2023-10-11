/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Component, Fragment, createRef, useMemo } from '@wordpress/element';
import {
  ExternalLink,
  ToggleControl,
  Button,
  TextControl,
  Notice,
  Spinner,
  withSpokenMessages,
} from '@wordpress/components';
import { LEFT, RIGHT, UP, DOWN, BACKSPACE, ENTER } from '@wordpress/keycodes';
import { getRectangleFromRange } from '@wordpress/dom';
import { prependHTTP, safeDecodeURI, filterURLForDisplay } from '@wordpress/url';
import {
  create,
  insert,
  isCollapsed,
  applyFormat,
  getTextContent,
  slice,
} from '@wordpress/rich-text';
import { URLPopover } from '@wordpress/block-editor';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import URLInput from '../url-input';
import './style.scss';

/**
 * Internal dependencies
 */
import { createLinkFormat, isValidHref } from './utils';

const stopKeyPropagation = ( event ) => event.stopPropagation();
const { useAnchor } = wp.richText;

function isShowingInput( props, state ) {
  return props.addingLink || state.editLink;
}

const LinkEditor = ( { value, onChangeInputValue, onKeyDown, submitLink, autocompleteRef } ) => (
  // Disable reason: KeyPress must be suppressed so the block doesn't hide the toolbar
  /* eslint-disable jsx-a11y/no-noninteractive-element-interactions */
  <form
    className="editor-format-toolbar__link-container-content block-editor-format-toolbar__link-container-content"
    onKeyPress={ stopKeyPropagation }
    onKeyDown={ onKeyDown }
    onSubmit={ submitLink }
  >
    <URLInput
      value={ value }
      onChange={ onChangeInputValue }
      autocompleteRef={ autocompleteRef }
      autoFocus={ false }
    />
    <Button icon="editor-break" label={ __( 'Insert Pretty Link' ) } type="submit" />
  </form>
  /* eslint-enable jsx-a11y/no-noninteractive-element-interactions */
);

const LinkViewerUrl = ( { url } ) => {
  const prependedURL = prependHTTP( url );
  const linkClassName = classnames( 'editor-format-toolbar__link-container-value block-editor-format-toolbar__link-container-value', {
    'has-invalid-link': ! isValidHref( prependedURL ),
  } );

  if ( ! url ) {
    return <span className={ linkClassName }></span>;
  }

  return (
    <ExternalLink
      className={ linkClassName }
      href={ url }
    >
      { filterURLForDisplay( safeDecodeURI( url ) ) }
    </ExternalLink>
  );
};

const URLPopoverAtLink = ( { isActive, addingLink, value, contentRef, ...props } ) => {
  const anchorRect = useMemo( () => {
    const selection = window.getSelection();
    const range = selection.rangeCount > 0 ? selection.getRangeAt( 0 ) : null;
    if ( ! range ) {
      return;
    }

    if ( addingLink ) {
      return getRectangleFromRange( range );
    }

    let element = range.startContainer;

    // If the caret is right before the element, select the next element.
    element = element.nextElementSibling || element;

    while ( element.nodeType !== window.Node.ELEMENT_NODE ) {
      element = element.parentNode;
    }

    const closest = element.closest( 'a' );
    if ( closest ) {
      return closest.getBoundingClientRect();
    }
  }, [ isActive, addingLink, value.start, value.end ] );

  if ( ! anchorRect && ! useAnchor ) {
    return null;
  }

  let anchorProps;

  if ( useAnchor ) {
    // Prefer useAnchor over useAnchorRef, this is WP 6.1+ only
    anchorProps = {
      anchor: useAnchor( {
        editableContentElement: contentRef.current,
        value,
        settings: {
          tagName: 'a'
        },
      } )
    };
  } else {
    anchorProps = {
      anchorRect: anchorRect,
    };
  }

  return <URLPopover { ...anchorProps } { ...props } />;
};

const LinkViewer = ( { url, editLink } ) => {
  return (
    // Disable reason: KeyPress must be suppressed so the block doesn't hide the toolbar
    /* eslint-disable jsx-a11y/no-static-element-interactions */
    <div
      className="editor-format-toolbar__link-container-content block-editor-format-toolbar__link-container-content"
      onKeyPress={ stopKeyPropagation }
    >
      <LinkViewerUrl url={ url } />
      <Button icon="edit" label={ __( 'Edit' ) } onClick={ editLink } />
    </div>
    /* eslint-enable jsx-a11y/no-static-element-interactions */
  );
};

const createNewPrettyLink = (target, slug) => {
  return new Promise((resolve, reject) => {
    jQuery.post(
      ajaxurl,
      {
        action: 'prli_create_pretty_link',
        target: target,
        slug: slug,
        redirect: '',
        nofollow: 1,
        tracking: 1,
        sponsored: 0
      },
      (data, textStatus, xhr) => {
        'true' === data ? resolve(data) : reject(data);
      }
      ).fail(error => {
        reject(error);
      });
  });
}

class InlineLinkUI extends Component {
  constructor() {
    super( ...arguments );

    this.editLink = this.editLink.bind( this );
    this.submitLink = this.submitLink.bind( this );
    this.onKeyDown = this.onKeyDown.bind( this );
    this.onChangeInputValue = this.onChangeInputValue.bind( this );
    this.setNoFollow = this.setNoFollow.bind( this );
    this.setIsSponsored = this.setIsSponsored.bind( this );
    this.setLinkTarget = this.setLinkTarget.bind( this );
    this.onClickOutside = this.onClickOutside.bind( this );
    this.resetState = this.resetState.bind( this );
    this.autocompleteRef = createRef();

    this.state = {
      noFollow: false,
      opensInNewWindow: false,
      isSponsored: false,
      inputValue: '',
      newLinkUrl: '',
      newLinkSlug: '',
      creatingLink: false,
      createdLink: false,
      createdLinkError: false
    };
  }

  static getDerivedStateFromProps( props, state ) {
    const { activeAttributes: { url, target, isSponsored } } = props;
    const opensInNewWindow = target === '_blank';

    if ( ! isShowingInput( props, state ) ) {
      if ( url !== state.inputValue ) {
        return { inputValue: url };
      }

      if ( opensInNewWindow !== state.opensInNewWindow ) {
        return { opensInNewWindow };
      }
    }

    return null;
  }

  onKeyDown( event ) {
    if ( [ LEFT, DOWN, RIGHT, UP, BACKSPACE, ENTER ].indexOf( event.keyCode ) > -1 ) {
      // Stop the key event from propagating up to ObserveTyping.startTypingInTextField.
      event.stopPropagation();
    }
  }

  onChangeInputValue( inputValue ) {
    this.setState( { inputValue } );
  }

  setLinkTarget( opensInNewWindow ) {
    const { activeAttributes: { url = '' }, value, onChange } = this.props;

    this.setState( { opensInNewWindow } );

    // Apply now if URL is not being edited.
    if ( ! isShowingInput( this.props, this.state ) ) {
      const selectedText = getTextContent( slice( value ) );

      onChange( applyFormat( value, createLinkFormat( {
        url,
        opensInNewWindow,
        text: selectedText,
      } ) ) );
    }
  }

  setNoFollow( noFollow ) {
    const { activeAttributes: { url = '' }, value, onChange } = this.props;

    this.setState( { noFollow } );

    // Apply now if URL is not being edited.
    if ( ! isShowingInput( this.props, this.state ) ) {
      const selectedText = getTextContent( slice( value ) );

      onChange( applyFormat( value, createLinkFormat( {
        url,
        opensInNewWindow,
        text: selectedText,
        noFollow,
        isSponsored
      } ) ) );
    }
  }

  setIsSponsored( isSponsored ) {
    const { activeAttributes: { url = '' }, value, onChange } = this.props;

    this.setState( { isSponsored } );

    // Apply now if URL is not being edited.
    if ( ! isShowingInput( this.props, this.state ) ) {
      const selectedText = getTextContent( slice( value ) );

      onChange( applyFormat( value, createLinkFormat( {
        url,
        opensInNewWindow,
        text: selectedText,
        noFollow,
        isSponsored
      } ) ) );
    }
  }

  editLink( event ) {
    this.setState( { editLink: true } );
    event.preventDefault();
  }

  submitLink( event ) {
    const { isActive, value, onChange, speak } = this.props;
    const { inputValue, opensInNewWindow, noFollow, isSponsored } = this.state;
    const url = prependHTTP( inputValue );
    const selectedText = getTextContent( slice( value ) );
    const format = createLinkFormat( {
      url,
      opensInNewWindow,
      text: selectedText,
      noFollow,
      isSponsored
    } );

    event.preventDefault();

    if ( isCollapsed( value ) && ! isActive ) {
      const toInsert = applyFormat( create( { text: url } ), format, 0, url.length );
      onChange( insert( value, toInsert ) );
    } else {
      onChange( applyFormat( value, format ) );
    }

    this.resetState();

    if ( ! isValidHref( url ) ) {
      speak( __( 'Warning: the link has been inserted but may have errors. Please test it.' ), 'assertive' );
    } else if ( isActive ) {
      speak( __( 'Link edited.' ), 'assertive' );
    } else {
      speak( __( 'Link inserted.' ), 'assertive' );
    }
  }

  onClickOutside( event ) {
    // The autocomplete suggestions list renders in a separate popover (in a portal),
    // so onClickOutside fails to detect that a click on a suggestion occurred in the
    // LinkContainer. Detect clicks on autocomplete suggestions using a ref here, and
    // return to avoid the popover being closed.
    const autocompleteElement = this.autocompleteRef.current;
    if ( autocompleteElement && autocompleteElement.contains( event.target ) ) {
      return;
    }

    this.resetState();
  }

  resetState() {
    this.props.stopAddingLink();
    this.setState( { editLink: false } );
  }

  render() {
    const { isActive, activeAttributes: { url }, addingLink, value, contentRef } = this.props;

    if ( ! isActive && ! addingLink ) {
      return null;
    }

    const { inputValue, noFollow, opensInNewWindow, isSponsored, newLinkUrl, newLinkSlug, creatingLink, createdLink, createdLinkError } = this.state;
    const showInput = isShowingInput( this.props, this.state );

    return (
      <URLPopoverAtLink
        className="pretty-link-inserter"
        value={ value }
        isActive={ isActive }
        addingLink={ addingLink }
        onClickOutside={ this.onClickOutside }
        onClose={ this.resetState }
        focusOnMount={ showInput ? 'firstElement' : false }
        contentRef={ contentRef }
        renderSettings={ () => (
          <Fragment>
            <div>
              <ToggleControl
                label={ __( 'Open in New Tab' ) }
                checked={ opensInNewWindow }
                onChange={ this.setLinkTarget }
              />
              <ToggleControl
                label={ __( 'Nofollow' ) }
                checked={ noFollow }
                onChange={ this.setNoFollow }
              />
              <ToggleControl
                label={ __( 'Sponsored Link' ) }
                checked={ isSponsored }
                onChange={ this.setIsSponsored }
              />
            </div>
            <div className="pretty-link-inserter-form-container">
              {
                createdLink && (
                  <Notice status="success" onRemove={() => this.setState({createdLink: false})}>
                        <p>{__( 'Pretty Link created successfully.', 'memberpress' )}</p>
                    </Notice>
                )
              }
              {
                createdLinkError && (
                  <Notice status="error" onRemove={() => this.setState({createdLink: false, createdLinkError: false})}>
                        <p>{__( 'Pretty Link could not be created. Please try a slug that is not already used.', 'memberpress' )}</p>
                    </Notice>
                )
              }
              <strong>{__('New Pretty Link', 'pretty-link')}</strong>
              <form onSubmit={(event) => {
                event.preventDefault();
                // Send request to create new Pretty Link
                this.setState({
                  creatingLink: true,
                  createdLinkError: false,
                });
                createNewPrettyLink( newLinkUrl, newLinkSlug )
                  .then(data => {
                    this.setState({
                      createdLink: true,
                      creatingLink: false,
                      inputValue: plEditor.homeUrl + newLinkSlug,
                      newLinkUrl: '',
                      newLinkSlug: ''
                    });
                  })
                  .catch(error => {
                    this.setState({
                      createdLink: false,
                      creatingLink: false,
                      createdLinkError: true,
                    });
                  });
              }}>
                <p>
                  <TextControl
                        placeholder="URL"
                        className="pretty-link-new-link-url"
                        value={newLinkUrl}
                        onChange={ ( newLinkUrl ) => {
                          this.setState( { newLinkUrl } );
                        } }
                    />
                </p>
                  <p>
                    <TextControl
                          placeholder="Slug"
                          className="pretty-link-new-link-slug"
                          value={newLinkSlug}
                          onChange={ ( newLinkSlug ) => {
                            this.setState( { newLinkSlug } );
                          } }
                      />
                  </p>
                  <p>
                    <button
                      className="pretty-link-submit-new-link components-button is-button is-primary"
                      onClick={ () => {
                        console.log('Creating new Pretty Link...');
                      } }
                    >
                      { __( 'Create New Pretty Link', 'pretty-link' ) }
                    </button>
                    {
                      creatingLink && (
                        <Spinner />
                      )
                    }
                  </p>
              </form>
            </div>
          </Fragment>
        ) }
      >
        { showInput ? (
          <LinkEditor
            value={ inputValue }
            onChangeInputValue={ this.onChangeInputValue }
            onKeyDown={ this.onKeyDown }
            submitLink={ this.submitLink }
            autocompleteRef={ this.autocompleteRef }
          />
        ) : (
          <LinkViewer
            url={ url }
            editLink={ this.editLink }
          />
        ) }
      </URLPopoverAtLink>
    );
  }
}

export default withSpokenMessages( InlineLinkUI );
