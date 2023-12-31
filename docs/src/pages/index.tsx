import Link from '@docusaurus/Link'
import useDocusaurusContext from '@docusaurus/useDocusaurusContext'
import HomepageFeatures from '@site/src/components/HomepageFeatures'
import Layout from '@theme/Layout'
import MDXContent from '@theme/MDXContent'
import clsx from 'clsx'
import React from 'react'

import styles from './index.module.css'

function HomepageHeader() {
    const { siteConfig } = useDocusaurusContext()
    return (
        <header className={clsx('hero hero--primary', styles.heroBanner)}>
            <div className="container">
                <h1 className="hero__title">{'Code Stencil'}</h1>
                <p className="hero__subtitle">{siteConfig.tagline}</p>
                <img src={'img/comparison.png'} height={'400px'} style={{objectFit: 'contain'}}/>
                <div className={styles.buttons}>
                    <Link
                        className="button button--secondary button--lg"
                        to="/docs/category/walkthrough">
                        View Docs 📚
                    </Link>
                </div>
            </div>
        </header>
    )
}

export default function Home(): JSX.Element {
    const { siteConfig } = useDocusaurusContext()
    return (
        <MDXContent>
            <Layout
                title={siteConfig.title}
                description={siteConfig.tagline}>
                <HomepageHeader/>
                <main>
                    <HomepageFeatures/>
                </main>
            </Layout>
        </MDXContent>
    )
}
