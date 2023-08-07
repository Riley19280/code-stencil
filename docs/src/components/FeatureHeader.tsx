import React from 'react'

export default function FeatureHeader({ anchor, children, title }) {
    return (
        <div className='feature-header' style={{display: 'flex', alignItems: 'center'}}>
            { anchor && <a className="anchor" name={{ anchor }}></a>}
            <span style={{ fontSize: '40px' }}>📚</span>
            <span style={{ paddingLeft: '4px', ...(title ? {
                    fontSize: 'var(--ifm-h3-font-size)',
                    fontWeight: 'bold'
                } : {})}}>
              {children}
            </span>
        </div>
    )
}
